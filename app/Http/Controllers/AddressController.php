<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    private function getContact(User $user, int $contactId): Contact
    {
        $contact = Contact::where('user_id', $user->id)->where('id', $contactId)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $addressId): Address
    {
        $address = Address::where('contact_id', $contact->id)->where('id', $addressId)->first();
        if(!$address){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $contactId, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $contactId, int $addressId): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);

        $address = $this->getAddress($contact, $addressId);

        return new AddressResource($address);
    }

    public function update(int $contactId, int $addressId, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);

        $address = $this->getAddress($contact, $addressId);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $contactId, int $addressId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);
        $address = $this->getAddress($contact, $addressId);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function list(int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $contactId);
        $addresses = Address::where('contact_id', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }

}
