<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
    
    public function storagePath(){
        
        return "public/users/user_{$this->id}";
    }

    public function saveFile($file) {

        $this->deleteFile();
        $path = $this->storagePath();
        return $file->move(storage_path('app/'.$path), 'personal_photo.'.$file->getClientOriginalExtension());
    }

    public function urlFile() {

        $fileURL = '';
        $path = $this->storagePath();
        $storageFiles = Storage::files($path);
        foreach ($storageFiles as $file) {
            $fileURL = asset(str_replace('public', 'storage', $file)). '?code=' . md5_file(storage_path('app/'.$path).'/'.basename($file));
        }
        return $fileURL;
    }

    public function deleteFile() {

        $path = $this->storagePath();
        $files = Storage::files($path);
        foreach ($files as $file) {
            $name = explode('.', basename($file));
            if ($name[0] == 'personal_photo') {
                Storage::delete($file);
            }
        }
        return 'Deleted';
    }
}
