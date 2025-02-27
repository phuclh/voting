<?php namespace App\Oauth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class DeKnotProvider extends AbstractProvider implements ProviderInterface
{
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(Str::finish(config('voting.deknot_passport_server'), '/') . 'oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return Str::finish(config('voting.deknot_passport_server'), '/') . 'oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(Str::finish(config('voting.deknot_passport_server'), '/') . 'api/user');

        $user = $response->json();

        $user['is_verified'] = !empty(Arr::get($user, 'email_verified_at'));

        return $user;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'    => $user['id'],
            'name'  => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email')
        ]);
    }
}
