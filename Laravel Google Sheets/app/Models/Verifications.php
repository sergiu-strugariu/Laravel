<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifications extends Model
{
    use HasFactory;

    const TIP = 'Organizator';

    public static function IsUserAuth($request)
    {
        $user = $request->session()->get('user');

        if ($user) {
            $user = Sheets::getSheetRowById('Utilizatori', $user['id']);

            return true;
        }

        return false;
    }

    public static function IsUserOrganizator($request)
    {
        $user = self::GetUserAuth($request);
        $user = Sheets::getSheetRowById("Utilizatori", $user['id']);

        if ($user['Tip'] === self::TIP) {
            return true;
        }

        return false;
    }

    public static function GetUserAuth($request)
    {
        $user = $request->session()->get('user');

        if ($user) {
            $user = Sheets::getSheetRowByValue('Utilizatori', $user['id']);
            return $user;
        }

        return;
    }

    public static function Base64ToImage($request)
    {
        $file = $request->image;
        $fileParts = explode(';base64', $file);
        $fileTypeAux = explode('image/', $fileParts[0]);
        $fileType = $fileTypeAux[1];
        $fileBase64 = base64_decode($fileParts[1]);
        $file = uniqid() . '.' . $fileType;
        $filePath = storage_path() . '\app\public\Concursuri\\' . $file;
        file_put_contents($filePath, $fileBase64);

        return $file;
    }
}
