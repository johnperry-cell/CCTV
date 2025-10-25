<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\FileUploadRequest;

use App\Models\User;
use App\Models\Gender;
use App\Models\Address;
use App\Models\Image;

trait UserTrait {
    use FilesTrait, ImageTrait;

    public function getGenders() {
        $genders = Gender::select("name", "id")
            ->when(!config("app.debug") || config("app.env") == "production", function ($q) {
                $q->where("name", "!=", "Attack Helicopter");
            })
            ->get();

        return $genders->map(function ($gender) {
            return [
                "name" => $gender->name,
                "id" => $gender->hash,
            ];
        });
    }

    //region Account Management
    protected function update_username(array $data, User $user): User {
        $user->update([
            "username" => strtolower($data["username"]),
        ]);
        return $user;
    }

    protected function update_email(array $data, User $user, $verified = false): User {
        $user->update([
            "email" => strtolower($data["email"]),
            "email_verified_at" => $verified ? now() : null,
        ]);
        if (!$verified) {
            $user->sendEmailVerificationNotification();
        }
        return $user;
    }

    protected function update_password(array $data, User $user) {
        $user->update([
            "password" => Hash::make($data["password"]),
        ]);
        return $user;
    }
    //endregion

    //region Profile Management
    protected function update_profile(array $data, User $user): User {
        $user->profile()->updateOrCreate(
            ["user_id" => $user->id],
            [
                "first_name" => $data["first_name"],
                "middle_name" => $data["middle_name"],
                "last_name" => $data["last_name"],
                "suffix" => $data["suffix"],
                "nickname" => $data["nickname"],
                "gender_id" => $data["gender"],
                "birthdate" => Carbon::parse($data["birthdate"]),
            ]
        );
        return $user;
    }

    protected function update_address(array $data, User $user, Address $address = null): User {
        $profile = $user->profile()->firstOrCreate();
        $addressCtr = $profile->addresses()->count();
        $exists = $profile
            ->addresses()
            ->where("location", $data["location"])
            ->where("barangayCode", $data["barangay"])
            ->when($address, function ($q) use ($address) {
                $q->where("id", "!=", $address->id);
            })
            ->exists();

        if ($exists) {
            abort(422, "Address already exists!");
        }

        $modelInsert = [
            "location" => $data["location"],
            "barangayCode" => $data["barangay"],
            "zipCode" => $data["zipCode"],
        ];

        if ($address) {
            $address->update($modelInsert);
        } else {
            $profile->addresses()->create([...$modelInsert, "isMain" => $addressCtr == 0]);
        }

        return $user;
    }

    protected function set_main_address(User $user, Address $address): User {
        $addr = $user->profile
            ->addresses()
            ->where("id", $address->id)
            ->first();
        if (!$addr) {
            abort(422, "Unknown address!");
        }
        $user->profile->addresses()->update(["isMain" => false]);
        $addr->update(["isMain" => true]);
        $user->refresh();
        return $user;
    }

    protected function delete_address(User $user, Address $address): User {
        $addr = $user->profile
            ->addresses()
            ->where("id", $address->id)
            ->first();
        if (!$addr) {
            abort(422, "Unknown address!");
        }
        $addr->delete();
        return $user;
    }

    protected function add_avatar(FileUploadRequest $request, User $user) {
        $location = "users/" . $user->hash . "/avatar";
        $upload = $this->uploadFileRequest($request, $location, "required|image");
        if (isset($upload["file"])) {
            $file = $upload["file"];
            $images = $user->profile()->firstOrCreate()->images();
            $image = $images->create([
                "file_id" => $file->id,
                "alt" => "avatar_" . $user->hash,
            ]);

            $count = $images->count();
            if ($count == 1) {
                $images->updateExistingPivot($image->id, ["primary" => true]);
            }

            if ($file->ext != "gif" && $file->ext != "svg") {
                $this->createThumbnails($image);
                $this->resizeImage($image);
            }
        }
        return $upload["result"];
    }

    protected function change_avatar(User $user, Image $image): User {
        $validUserImage = $user->profile
            ->images()
            ->wherePivot("image_id", $image->id)
            ->first();
        if (!$validUserImage) {
            abort(422, "Image not found!");
        }
        $user->profile()->firstOrCreate()->setPrimaryImage($image);
        return $user;
    }

    protected function delete_avatar(User $user, Image $image): User {
        $validUserImage = $user->profile
            ->images()
            ->wherePivot("image_id", $image->id)
            ->wherePivot("primary", false)
            ->first();
        if (!$validUserImage) {
            abort(422, "Image not found!");
        }
        $user->profile->images()->detach($image->id);
        $image->delete();
        return $user;
    }
    //endregion
}
