<?php

namespace App\Support;

class Telepon{
    public static function rapikan(string $nomorMentah): string
    {
        $nomor = preg_replace('/[\s\-\.\(\)]/', '', trim($nomorMentah));

        if(str_starts_with($nomor, '+62')) {
            $nomor = '0' . substr($nomor, 3);
        } elseif (str_starts_with($nomor, '62')){
            $nomor = '0' . substr($nomor, 2);
        }

        return $nomor;
    }

    public static function valid(string $nomorMentah): bool{
        return (bool) preg_match('/^08[0-9]{8,11}$/', trim($nomorMentah));
    }
}