<?php

namespace App\Services;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\campaign_kpiRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\userRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use function Symfony\Component\Routing\Loader\load;

class UserService
{
    use AuthorizesRequests;

    protected $userRepository;
    protected $emailRepository;
    public function __construct(userRepository $userRepository, EmailVerficationRepository $emailRepository)
    {
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
    }
    protected function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    public function register(UserRequest $request): array
    {
        $user = $this->userRepository->create($request->validated());
        $code = $this->generateVerificationCode();
        $this->emailRepository->deleteByEmail($request->email);
        $verification = $this->emailRepository->create($request->email, $code);
        Mail::to($user->email)->send(new EmailVerificationMail($code));
        return [
            'user' => $user,
            'verification' => $verification,
            'message' => 'Registration success. Please check your email to verify your account',
            'code' => 201
        ];
    }

    public function Verify(EmailVerificationRequest $request){
        $emailverfication = $this->emailRepository->exists($request);
        if (!$emailverfication) {
            return response()->json(['message' => 'Invalid or expired Verification code'], 400);
        }
        $user = $this->userRepository->getByEmail($request->email);
        $user->email_verified_at = Carbon::now();
        $user->save();
        $emailverfication->delete();
        return [
            'user' => $emailverfication,
            'message' => 'تم تاكيد حسابك بنجاح يمكنك الان تسجيل الدخول',
            'code' => 201,
        ];

    }
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return [
                'user' => null,
                'message' => 'Invalid credentials',
                'code' => 401
            ];
        }

        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            return [
                'user' => null,
                'message' => 'Email not verified',
                'code' => 403
            ];
        }

        $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        $user->givePermissionTo($permissions);

        $user = User::with('roles.permissions', 'permissions')->find($user->id);

        $user = $this->appendRolesAndPermission($user);

        $user['token'] = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'message' => 'Login successful',
            'code' => 200
        ];
           }  private function appendRolesAndPermissions($user)
{
    return [

        // roles فقط أسماء
        'roles' => $user->getRoleNames(),

        // permissions من الأدوار
        'permissions' => $user->getPermissionsViaRoles()->pluck('name')->values(),
    ];
}
    public function logout()
    {
        $user = Auth::user();
        if (!is_null($user)) {
            $user->delete();
            $message = 'user Logged out  Successfully';
            $code = 200;
        } else {
            $message = 'invaild token';
            $code = 404;
        }
        return (['user' => $user, 'message' => $message, 'code' => $code]);
    }
    public function createUser(addUserRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $this->userRepository->create_User($request->toArray());
            $user->assignRole($request->role);
            $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
            $user = User::with('roles.permissions', 'permissions')->find($user->id);
            $user = User::query()->find($user->id);
            $user = $this->appendRolesAndPermission($user);
            return [
                'user' => $user,
                'message' => 'Success',
                'code' => 200
            ];});}
    private function appendRolesAndPermission($user)
    {
        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = $role->name;
        }
        unset($user['roles']);
        $user['roles'] = $roles;
        $permissions = [];
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission->name;
        }
        unset($user['permissions']);
        $user['permissions'] = $permissions;

        return $user;
    }

    public function getVisibleUsers($authUser)
    {

        $role = $authUser->getRoleNames()->first();

        $map = [
            'Campaign Manager' => ['Campaign Employee', 'Volunteer Manager'],
            'Evaluation Manager' => ['Evaluation Officer'],
        ];

        $allowedRoles = $map[$role] ?? [];

        $user= $this->userRepository->getByRolesAndDepartment(
            $allowedRoles,
            $authUser->department_id
        );
        return [
            'user' =>  UserResource::collection($user),
            'message' => ' employees successfully',
            'code' => 200
        ];

    }

    public function searchEmployee($request){
            $user = $this->userRepository->searchEmployee($request);
        return [
            'user' => $user,
            'message' => 'employees retrieved successfully',
            'code' => 200
        ];
    }
    public function UpdateEmployee( $request,$id)
    {
        $user = $this->userRepository->getById($id);
        if(!$user) {
    return [
        'user' => null,
        'message' => 'this user not found',
        'code' => 404
    ];
}
        $this->authorize('update', $user);
        $user = $this->userRepository->UpdateEmployee($request->validated(), $id);
        return [
            'user' =>  new UserResource($user),
            'message' => 'success',
            'code' => 200
        ];
    }
    //عرض تفاصيل موظف
    public function ShowdetailEmployee($id)
    {
        $user = $this->userRepository->getById($id);

        if (!$user) {
            return [
                'user' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        $this->authorize('view', $user);

        return [
            'user' =>  new UserDetailResource($user),
            'message' => 'User retrieved successfully',
            'code' => 200
        ];
    }

}
