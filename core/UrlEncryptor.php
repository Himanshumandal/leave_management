<?php

class UrlEncryptor
{
    private const METHOD = 'aes-256-gcm';

    private static function key(): string
    {
        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Keep this key secret.
        | Do NOT change it after encrypted URLs are already in use.
        |--------------------------------------------------------------------------
        */

        return hash(
            'sha256',
            'YOUR-VERY-SECRET-APPLICATION-KEY',
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ENCRYPT
    |--------------------------------------------------------------------------
    */

    public static function encrypt(int $value): string
    {
        $ivLength =
            openssl_cipher_iv_length(
                self::METHOD
            );

        $iv =
            random_bytes($ivLength);

        $tag = '';

        $encrypted =
            openssl_encrypt(
                (string) $value,
                self::METHOD,
                self::key(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

        if ($encrypted === false) {

            throw new RuntimeException(
                'Unable to encrypt value.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Combine:
        | IV + TAG + ENCRYPTED DATA
        |--------------------------------------------------------------------------
        */

        $data =
            $iv .
            $tag .
            $encrypted;


        /*
        |--------------------------------------------------------------------------
        | URL SAFE BASE64
        |--------------------------------------------------------------------------
        */

        return rtrim(
            strtr(
                base64_encode($data),
                '+/',
                '-_'
            ),
            '='
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DECRYPT
    |--------------------------------------------------------------------------
    */

    public static function decrypt(
        string $value
    ): ?int {

        if ($value === '') {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Decode
        |--------------------------------------------------------------------------
        */

        $data =
            base64_decode(
                strtr(
                    $value,
                    '-_',
                    '+/'
                ),
                true
            );


        if ($data === false) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Get lengths
        |--------------------------------------------------------------------------
        */

        $ivLength =
            openssl_cipher_iv_length(
                self::METHOD
            );

        $tagLength = 16;


        if (
            strlen($data)
            <
            ($ivLength + $tagLength)
        ) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Extract IV
        |--------------------------------------------------------------------------
        */

        $iv =
            substr(
                $data,
                0,
                $ivLength
            );


        /*
        |--------------------------------------------------------------------------
        | Extract TAG
        |--------------------------------------------------------------------------
        */

        $tag =
            substr(
                $data,
                $ivLength,
                $tagLength
            );


        /*
        |--------------------------------------------------------------------------
        | Extract encrypted value
        |--------------------------------------------------------------------------
        */

        $encrypted =
            substr(
                $data,
                $ivLength + $tagLength
            );


        /*
        |--------------------------------------------------------------------------
        | Decrypt
        |--------------------------------------------------------------------------
        */

        $decrypted =
            openssl_decrypt(
                $encrypted,
                self::METHOD,
                self::key(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );


        if ($decrypted === false) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate integer
        |--------------------------------------------------------------------------
        */

        if (!ctype_digit($decrypted)) {

            return null;
        }


        $id =
            (int) $decrypted;


        return $id > 0
            ? $id
            : null;
    }

    public static function isEncrypted(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        return self::decrypt($value) !== null;
    }
}