<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- เพิ่มบรรทัดนี้ครับ
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Common\Personnel;

class MicrosoftController extends Controller
{
    public function redirectToMicrosoft(Request $request){
       $tenant = config('services.microsoft.tenant_id');
        $clientId = config('services.microsoft.client_id');
        $redirectUri = config('services.microsoft.redirect');
       

        $url = "https://login.microsoftonline.com/$tenant/oauth2/v2.0/authorize";

        $query = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email User.Read'
        ]);

        return redirect("$url?$query");
    }

    public function handleMicrosoftCallback(Request $request){
        if (!$request->has('code')) {
            return response()->json(['error'=>'Authorization code missing'],400);
        }

        $tenant = config('services.microsoft.tenant_id');

        $tokenEndpoint = "https://login.microsoftonline.com/$tenant/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenEndpoint, [

            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'code' => $request->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.microsoft.redirect')

        ]);

        $tokenData = $response->json();

        if(!isset($tokenData['access_token'])){
            return response()->json($tokenData,500);
        }

        $accessToken = $tokenData['access_token'];

        // call Microsoft Graph
        $profile = Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me')
            ->json();

        $email = $profile['mail'] ?? null;
        if (!$email) {
            return response()->json(['error' => 'No email found in Microsoft profile'], 403);
        }

        // 2. ค้นหา person ที่มี email นี้
        $person = Personnel::where('email', $email)->first();
       
        if (!$person) {
            // ถ้าไม่พบ email ใน personnals -> ไม่สามารถ login ได้
            return response()->json(['error' => 'ไม่พบข้อมูลบุคลากรในระบบ'], 403);
        }

        // 3. ค้นหา user ที่ผูกกับ person นี้
        $user = User::where('personnals_id', $person->id)->first();
       
        if (!$user) {
            // ถ้าพบใน personnals แต่ไม่มี user ให้สร้างใหม่
            $user = new User;
            $user->personnals_id = $person->id;
            // ตั้งรหัสผ่านสุ่ม (เพราะ login ผ่าน 365)
            //$user->password = Hash::make(Str::random(32)); 
            $user->save();
            
        }

        // 4. อัปเดตข้อมูลการ login และ login เข้าสู่ระบบ
        // เพิ่มเติม: ถ้าตาราง users มีฟิลด์ last_login_at ให้ใช้คำสั่ง update
        $user->updated_at = \Carbon\Carbon::now(); 
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect('/');
    }

}
