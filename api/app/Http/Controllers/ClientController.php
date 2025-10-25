<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdatePermissionRequest;
use App\Http\Requests\SuperRequest;

use App\Models\Client;

use App\Http\Resources\ClientResource;

use App\Enums\TokenAbility;

use App\Traits\PermissionsTrait;

class ClientController extends Controller {
    use PermissionsTrait;

    #region Admin Functions
    public function list(SuperRequest $request) {
        $limit = $request->input("limit", 10);
        $page = $request->input("page", 1);
        $search = $request->input("search");
        $clients = Client::withTrashed()
            ->when($search, function ($query) use ($search) {
                $query
                    ->where("name", "ilike", "%{$search}%")
                    ->orWhere("domain", "ilike", "%{$search}%")
                    ->orWhere("uuid", "ilike", "%{$search}%");
            })
            ->orderBy("id", "asc")
            ->paginates($limit, $page);

        $clients["data"] = ClientResource::collection($clients["data"]);

        return $clients;

        // return ClientResource::collection($clients);
    }

    public function store(CreateClientRequest $request) {
        $secret = $this->random_password(40);
        $client = Client::create([
            "name" => $request->name,
            "domain" => $request->domain,
            "secret" => bcrypt($secret),
        ]);

        trail("Client Management")->notice("Client created", newData: ["name" => $request->name, "domain" => $request->domain, "id" => $client->uuid]);

        return [
            "data" => ClientResource::make($client),
            "secret" => $secret,
            "message" => "Client created successfully!",
        ];
    }

    public function update(CreateClientRequest $request, string $client) {
        $client = Client::withTrashed()->where("uuid", $client)->first();
        $old = [
            "name" => $client->name,
            "domain" => $client->domain,
        ];
        $client->update([
            "name" => $request->name,
            "domain" => $request->domain,
        ]);

        trail("Client Management")->info("Client updated: " . $client->uuid, newData: ["name" => $request->name, "domain" => $request->domain], oldData: $old);

        return [
            "data" => ClientResource::make($client),
            "message" => "Client updated successfully!",
        ];
    }

    public function regenerateSecret(SuperRequest $request, string $client) {
        $client = Client::withTrashed()->where("uuid", $client)->first();
        $secret = $this->random_password(40);
        $client->update(["secret" => bcrypt($secret)]);
        $client->tokens()->delete();

        trail("Client Management")->warning("Client secret regenerated for " . $client->uuid);

        return [
            "data" => ClientResource::make($client),
            "secret" => $secret,
            "message" => "Client secret regenerated successfully!",
        ];
    }

    public function toggle(SuperRequest $request, string $client) {
        $client = Client::withTrashed()->where("uuid", $client)->first();
        $message = "Client deleted successfully!";
        if ($client->trashed()) {
            $client->restore();
            $message = "Client restored successfully!";

            trail("Client Management")->notice("Client restored: " . $client->uuid);
        } else {
            $client->delete();

            trail("Client Management")->alert("Client deleted: " . $client->uuid);
        }

        return [
            "data" => ClientResource::make($client),
            "message" => $message,
        ];
    }

    public function permissions(SuperRequest $request) {
        $permissions = $this->getPermissions($request, "client");
        return $permissions;
    }

    public function updatePermissions(UpdatePermissionRequest $request, string $client) {
        $client = Client::withTrashed()->where("uuid", $client)->first();
        $client->permissions()->sync($request->permissions);

        return [
            "data" => ClientResource::make($client),
            "message" => "Client permissions updated successfully!",
        ];
    }
    #endregion

    #region Client Functions
    public function getToken(Request $request) {
        $client_id = $request->input("client_id");
        $client_secret = $request->input("client_secret");

        $client = Client::where("uuid", $client_id)->first();
        $domain = $request->headers->get("Origin");

        if ($client && trim($client->domain) == $domain && Hash::check($client_secret, $client->secret)) {
            $client->tokens()->delete();
            $tokens = $this->generateTokens($client, true);

            return [...$tokens, "token_type" => "Bearer"];
        }

        return response(
            [
                "message" => "Invalid credentials!",
            ],
            401
        );
    }

    public function refreshToken(Request $request) {
        $client = $request->user();
        $tokens = $this->generateTokens($client, true);

        return response([...$tokens, "token_type" => "Bearer"]);
    }

    #endregion

    private function random_password($length = 8) {
        $chars = [
            "upper" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "lower" => "abcdefghijklmnopqrstuvwxyz",
            "number" => "0123456789",
            "special" => "!@#$%^&*()_-=+;:,.?",
        ];
        $shuffle = join("", array_values($chars));
        $password = substr(str_shuffle($shuffle), 0, $length);
        return $password;
    }

    /**
     * Generate access and refresh tokens for the client
     *
     * @param Client $client
     * @param bool $removeId - Remove the client id from the token
     *
     * @return array
     */
    private function generateTokens(Client $client, bool $removeId = false) {
        $access = $client->createToken(
            Str::slug($client->name . " access token"),
            [TokenAbility::ACCESS_API->value],
            Carbon::now()->addMinutes(config("mitd.sanctum.ac_expiration"))
        );

        $refresh = $client->createToken(
            Str::slug($client->name . " refresh token"),
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->minutes(config("mitd.sanctum.rt_expiration"))
        );
        return [
            "abilities" => $client->getAllPermissions()->pluck("name"),
            "access_token" => $removeId ? explode("|", $access->plainTextToken)[1] : $access->plainTextToken,
            "refresh_token" => $removeId ? explode("|", $refresh->plainTextToken)[1] : $refresh->plainTextToken,
            "expires_in" => $access->accessToken->expires_at->timestamp - Carbon::now()->timestamp,
        ];
    }
}
