<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Events\UserLoggedIn;
use App\Modules\Authentication\Events\UserLoggedOut;
use App\Modules\Authentication\Http\Requests\LoginRequest;
use App\Modules\Authentication\Http\Requests\RegisterRequest;
use App\Modules\Authentication\Http\Requests\ResetPasswordRequest;
use App\Modules\Authentication\Http\Requests\SendResetPasswordLinkRequest;
use App\Modules\Authentication\Http\Transformers\UserTransformer;
use App\Modules\Authentication\Jobs\RequestPasswordReset;
use App\Modules\Authentication\Models\PasswordResetToken;
use App\Modules\Authentication\Models\User;
use Illuminate\Contracts\Auth\{Factory as AuthFactory, Guard, StatefulGuard};
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthController extends Controller
{
    private Guard|StatefulGuard $guard;

    public function __construct(AuthFactory $auth, UserTransformer $transformer)
    {
        $this->guard = $auth->guard('web');
        $this->transformer = $transformer;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$this->guard->attempt($request->credentials(), $request->shouldRemember())) {
            throw new AuthenticationException(__('auth.failed'));
        }

        event(new UserLoggedIn($this->guard->id()));

        return $this->respondWithItem($this->guard->user());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');

        $nickname = null;
        if ($request->get('nickname')) {
            $nickname = $request->get('nickname');
        }

        $user = User::create(Role::CONTRIBUTOR, $name, $email, $password, null, $nickname);

        $this->guard->login($user, false);

        event(new UserLoggedIn($this->guard->id()));

        return $this->respondWithItem($this->guard->user());
    }

    public function logout(): Response
    {
        $id = $this->guard->id();
        $this->guard->logout();

        event(new UserLoggedOut($id));

        return response()->noContent();
    }

    public function sendResetLinkEmail(SendResetPasswordLinkRequest $request): Response
    {
        $this->dispatch(
            new RequestPasswordReset($request->get('email'))
        );

        return response()->noContent();
    }

    public function validateResetToken(string $token): JsonResponse
    {
        $model = PasswordResetToken::retrieve($token);

        if ($model->expired()) {
            throw new ModelNotFoundException('Token expired.');
        }

        return $this->respondWithItem($model->user);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $token = PasswordResetToken::retrieve($request->get('token'));

        if ($token->expired()) {
            throw new BadRequestHttpException('Token expired.');
        }

        $user = $token->user;
        $user->changePassword($request->get('password'));

        Auth::login($token->user);

        event(new UserLoggedIn($this->guard->id()));

        $token->delete();

        return $this->respondWithItem($token->user);
    }

    public function user(): JsonResponse
    {
        return $this->respondWithItem($this->guard->user());
    }
}
