<?php

namespace App\Http\Controllers\Api;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ResponseInterface;

class AuthController extends Controller
{
    public function register(Request $request) {

        $validatedData = $request->validate([
            'name'  => 'required|max:55',
            'email' => 'email|required|unique:users,email',
            'password'  => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);

        dd ($user->createToken('authToken')); exit();

        $accessToken = $user->createToken('authToken')->accessTokenpost;

        return response()->json([
            'user'  => $user,
            'access_token'  => $accessToken
        ], 200);


    }

    public function login(Request $request) {

        $loginData = $request->validate([
            'email' => 'email|required',
            'password'  => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json([
                'message'   => 'Invalid Credentails'
            ]);
        }

        $client = new Client();
        try {
            $response = $client->post('http://dev.oauthusers.test/api/oauth/token', [
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => '3',
                    'client_secret' => 'yfYntKB0SqcnsyTgEVST6Ja5uc9NB3UQYauWg35l',
                    'username'      => 'yinghua5@gmail.com',
                    'password'      => '123123'
                ]
            ]);
        } catch (ClientException $e) {
            return response()->json ($e->getMessage());
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);

        $responseParams = [
            'code'  => 200,
            'status'    => 'success',
            'data'  => $responseBody
        ];


        return response()->json($responseParams, 200);

    }

    public function loginAsync (Request $request) {

        $loginData = $request->validate([
            'email' => 'email|required',
            'password'  => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json([
                'message'   => 'Invalid Credentails'
            ]);
        }

        $client = new Client();

        $promise = $client->postAsync('http://dev.oauthusers.test/api/oauth/token', [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => '3',
                'client_secret' => 'yfYntKB0SqcnsyTgEVST6Ja5uc9NB3UQYauWg35l',
                'username'      => 'yinghua5@gmail.com',
                'password'      => '123123'
            ]
        ])->then (function ($response)  {
            dd ($response);
            return response()->json($response);
        });

        return $promise->wait();

    }

}
