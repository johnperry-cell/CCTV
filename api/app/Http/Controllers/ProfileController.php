<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\User\UpdateUserEmailRequest;
use App\Http\Requests\User\UpdateUserUsernameRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UpdateUserAddressRequest;
use App\Http\Requests\User\UpdateUserPasswordRequest;

use App\Http\Requests\FileUploadRequest;

use App\Traits\UserTrait;

use App\Models\Image;
use App\Models\Address;

use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileImageResource;
use App\Http\Resources\AddressResource;

class ProfileController extends Controller {
    use UserTrait;

    public function updateEmail(UpdateUserEmailRequest $request) {
        $updated = $this->update_email($request->validated(), Auth::user());
        return new UserResource($updated);
    }

    public function updateUsername(UpdateUserUsernameRequest $request) {
        $updated = $this->update_username($request->validated(), Auth::user());
        return new UserResource($updated);
    }

    public function updatePassword(UpdateUserPasswordRequest $request) {
        $updated = $this->update_password($request->validated(), Auth::user());
        return new UserResource($updated);
    }

    public function updateProfile(UpdateUserProfileRequest $request) {
        $updated = $this->update_profile($request->validated(), Auth::user());
        $updated->refresh();
        return new UserResource($updated);
    }

    public function getAddresses() {
        $addresses = Auth::user()->profile()->first()->addresses()->get();
        return response(["data" => AddressResource::collection($addresses)]);
    }

    public function addAddress(UpdateUserAddressRequest $request) {
        $updated = $this->update_address($request->validated(), Auth::user());
        return new UserResource($updated);
    }

    public function updateAddress(UpdateUserAddressRequest $request, Address $address) {
        $updated = $this->update_address($request->validated(), Auth::user(), $address);
        return new UserResource($updated);
    }

    public function deleteAddress(Address $address) {
        Gate::authorize("updateOwnProfile", Auth::user());
        return new UserResource($this->delete_address(Auth::user(), $address));
    }

    public function setMainAddress(Address $address) {
        Gate::authorize("updateOwnProfile", Auth::user());
        return new UserResource($this->set_main_address(Auth::user(), $address));
    }

    public function getAvatars() {
        $images = Auth::user()->profile()->first()->images()->get();
        return response(["data" => ProfileImageResource::collection($images)]);
    }

    public function addAvatar(FileUploadRequest $request) {
        $user = Auth::user();
        Gate::authorize("updateOwnAvatar", $user);
        $uploaded = $this->add_avatar($request, $user);
        if ($uploaded["status"] == "complete") {
            return [...$uploaded, "data" => new UserResource($user)];
        }
        return $uploaded;
    }

    public function changeAvatar(Image $image) {
        $user = Auth::user();
        Gate::authorize("updateOwnAvatar", $user);
        return new UserResource($this->change_avatar($user, $image));
    }

    public function deleteAvatar(Image $image) {
        $user = Auth::user();
        Gate::authorize("updateOwnAvatar", $user);
        return new UserResource($this->delete_avatar($user, $image));
    }
}
