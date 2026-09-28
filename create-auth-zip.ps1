# create-auth-zip.ps1
# Generates auth-controllers.zip containing minimal Auth controller stubs
param()

$files = @{
    'app/Http/Controllers/Auth/AuthenticatedSessionController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return redirect('/');
    }

    public function store(Request $request)
    {
        // Minimal stub: in a real app authenticate the user here.
        return redirect('/');
    }

    public function destroy(Request $request)
    {
        // Minimal stub: in a real app logout the user here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/RegisteredUserController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return redirect('/');
    }

    public function store(Request $request)
    {
        // Minimal stub: create user logic goes here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/PasswordResetLinkController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return redirect('/');
    }

    public function store(Request $request)
    {
        // Minimal stub: send reset link logic goes here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/NewPasswordController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewPasswordController extends Controller
{
    public function create($token = null)
    {
        return redirect('/');
    }

    public function store(Request $request)
    {
        // Minimal stub: reset password logic goes here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/ConfirmablePasswordController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfirmablePasswordController extends Controller
{
    public function show()
    {
        return redirect('/');
    }

    public function store(Request $request)
    {
        // Minimal stub: confirm password logic goes here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/PasswordController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        // Minimal stub: update password logic goes here.
        return redirect('/');
    }
}
'@

    'app/Http/Controllers/Auth/EmailVerificationNotificationController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        // Minimal stub: trigger email verification notification.
        return redirect()->route('verification.notice');
    }
}
'@

    'app/Http/Controllers/Auth/EmailVerificationPromptController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request)
    {
        return new Response('<h1>Please verify your email address</h1>', 200);
    }
}
'@

    'app/Http/Controllers/Auth/VerifyEmailController.php' = @'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id = null, $hash = null)
    {
        // Minimal stub: in a real app verify the user's email here.
        return redirect('/');
    }
}
'@
}

$zipName = 'auth-controllers.zip'

# Ensure target directory structure exists and write files to a temp folder
$temp = Join-Path -Path (Get-Location) -ChildPath 'auth-temp'
if (Test-Path $temp) { Remove-Item $temp -Recurse -Force }
New-Item -ItemType Directory -Path $temp -Force | Out-Null

foreach ($rel in $files.Keys) {
    $content = $files[$rel]
    $full = Join-Path -Path $temp -ChildPath $rel
    $dir = Split-Path $full -Parent
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
    $content | Set-Content -Path $full -Encoding UTF8
}

# Create zip preserving relative paths
if (Test-Path $zipName) { Remove-Item $zipName -Force }
Compress-Archive -Path (Join-Path $temp '*') -DestinationPath $zipName -Force

Write-Output "Created $zipName in $(Get-Location)"
Write-Output "Upload $zipName to your host htdocs folder and extract so files land in app/Http/Controllers/Auth/"
