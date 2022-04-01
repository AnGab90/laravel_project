<?php
namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\Concerns\Has;

class PassportAuthController extends Controller
{
    public function users(){
        $users = User::get();
        return response($users);
    }
    public function register(UserRequest $request)
    {
        if($request->hasFile('avatar')){
            $upload_avatar = public_path('images/avatar');
            $new_name_avatar = time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move($upload_avatar, $new_name_avatar);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'avatar'=>$new_name_avatar,
                'password' => Hash::make($request->password)
            ]);
            $token = $user->createToken('Laravel')->accessToken;
            $user->reg_token = $token;
            $user->save();
            $this->emailVerification($user, $token);
            return response()->json(['token' => $token], 200);
        }
    }

    public function updateUser(Request $request, $id){

        $user = User::find($id);
        $user->name=$request->name;
        $user->email =$request->email;
        $user->password=Hash::make($request->password);

        if($request->hasFile('avatar')){
            $destination = 'images/avatar'.$user->avatar;
            if(File::exists($destination))
            {
                File::delete($destination);
            };
            $file = $request->file('avatar');
            $extension =$file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('images/avatar', $filename);
            $user->avatar = $filename;
        }
        $user->save();
        return response()->json($user);
    }

    public function emailVerification(User $user, $token){
        Mail::send('mail.verify', ['user' => $user, 'token' => $token], function ($m) use ($user){
            $m->to($user->email, $user->name)->subject('Please verify your account');
        });
    }

    public function verify(Request $request){
        $user = User::query()->where('email', '=', $request->email)->first();

        if($user->reg_token === $request->token){
            $user->reg_token = null;
            $user->email_verified_at = Carbon::now();
            $user->save();
            return response()->json(['message'=>'Finished']);
        }else{
            return response()->json(['error'=>'Something went wrong'], 500);
        }
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email,)->where('blocked', '=', false)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    public function block($id){
        $user =User:: find($id);
        $user->blocked=true;
        $user->save();
        return response()->json($user);
    }

    public function me() {
        return response()->json(['user' => auth()->user()]);
    }

    public function oneUser($id){
        $data=User::find($id);
        return response()->json($data);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
