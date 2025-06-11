<!-- 

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Rules\TemplateEmail;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function  index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

         try {
            $response = Http::post('http://localhost:8000/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                session(['access_token' => $data['access_token']]);
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('login')
                    ->with('error', 'Invalid credentials');
            }
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Auth service error: ' . $e->getMessage());
        }
    }

    
    // public function register()
    // {
    //     return view('auth.register');
    // }

    // public function processRegister(Request $request)
    // {
    //     $request->validate(
    //         [
    //         'email' => 'required', 'email', 'unique:users',
    //         'password' => 'required|confirmed',
    //         'age' =>  'required|integer|min:18',

    //         ]
    // );


    // User::create($request->all());

    //         return redirect()->route('login')
    //             ->with('success', 'you have registered successfully.');
        
    // }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
 --> -->
