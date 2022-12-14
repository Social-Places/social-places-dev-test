<?php

/**
 * @param string $role role name as <b>ROLE_sp_last_character_occurrence_substring</b>
 * @return string role name as <b>Suite Feature</b> or attempts to humanize the word with role removed
 */
function sp_string_humanize_role(string $role): string {
    if (stripos($role, 'role_') !== false) {
        return ucwords(strtolower(trim(str_ireplace('role', '', str_replace('_', ' ',$role)))));
    }

    return ucwords(strtolower(str_replace('_', ' ',$role)));
}

function sp_get_classname($class_or_object): string {
    if (is_string($class_or_object)) {
        return str_replace('Proxies\\__CG__\\', '', $class_or_object);
    }
    return str_replace('Proxies\\__CG__\\', '', get_class($class_or_object));
}

function sp_last_character_occurrence_substring($string, $searchCharacter, $includeSearchCharacter = false) {
    if (stripos($string, $searchCharacter) !== false) {
        return substr($string,
            strrpos($string, $searchCharacter) + (($includeSearchCharacter === false) ? (strlen($searchCharacter)) : (0)));
    }
    return $string;
}

function sp_get_namespaceless_classname($class_or_object): string {
    if (!is_string($class_or_object)) {
        $class_or_object = get_class($class_or_object);
    }
    return sp_last_character_occurrence_substring($class_or_object, '\\', false);
}

/**
 * @throws Exception
 */
function sp_random_str(int $length, string $keyspace = '0123456789abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()') {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function sp_string_normalize_camel_snake_kebab_to_words($string, $includesEscapedDashes = true, $ucwords = true) {
    $round1 = preg_replace('/([a-z])([A-Z0-9])/', "$1 $2", preg_replace('/([a-z0-9])([A-Z])/', "$1 $2", $string));
    $round2 = str_replace('_', ' ', $round1);
    $round3 = str_replace($includesEscapedDashes ? ['-', '\-\\', '-\\'] : '-', $includesEscapedDashes ? ['-\\', '-', ' '] : ' ', $round2);

    return $ucwords ? ucwords($round3, " \t\r\n\f\v-") : $round3;
}

function sp_sanitize_filename($string, $force_lowercase = true, $strict = false, $retain_space = false) {
    $strip = [
        '~',
        '`',
        '!',
        '@',
        '#',
        '$',
        '%',
        '^',
        '&',
        '*',
        '(',
        ')',
        '_',
        '=',
        '+',
        '[',
        '{',
        ']',
        '}',
        "\\",
        '|',
        ';',
        ':',
        '"',
        "'",
        "???",
        '&#8216;',
        '&#8217;',
        '&#8220;',
        '&#8221;',
        '&#8211;',
        '&#8212;',
        '????????',
        '????????',
        ',',
        '<',
        '.',
        '>',
        '/',
        '?',
        '??'
    ];
    $clean = trim(str_replace($strip, '', strip_tags($string)));
    if ($retain_space !== true) {
        $clean = preg_replace('/\s+/', '-', $clean);
    }
    $clean = ($strict) ? preg_replace('/[^a-zA-Z0-9]/', '', $clean) : $clean;
    if ($force_lowercase) {
        return function_exists('mb_strtolower') ? mb_strtolower($clean, 'UTF-8') : strtolower($clean);
    }
    return $clean;
}

function sp_unique_string_based_on_uniqid(
    string $prefix = '',
    bool $more_entropy = false,
    bool $shouldSanitize = false,
    bool $md5Encrypt = true
): string {
    $string = uniqid($prefix, $more_entropy);
    if ($shouldSanitize) {
        $string = sp_sanitize_filename($string);
    }
    if ($md5Encrypt) {
        $string = md5($string);
    }
    return $string;
}

function sp_strip_numeric($string): array|string|null {
    return preg_replace("/[0-9]/", "", $string);
}

function sp_string_contains_any(string $haystack, $needle): bool {
    if (!is_array($needle)) {
        return str_contains($haystack, $needle);
    }
    foreach ($needle as $innerNeedle) {
        if (str_contains($haystack, $innerNeedle)) {
            return true;
        }
    }
    return false;
}

function sp_replace_accented_characters(string $string): string {
    $unwanted_array = ['??' => 'S', '??' => 's', '??' => 'Z', '??' => 'z', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A',
        '??' => 'C', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'N', '??' => 'O', '??' => 'O',
        '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Y', '??' => 'B', '??' => 'Ss', '??' => 'a',
        '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'i',
        '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'o', '??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'u',
        '??' => 'u', '??' => 'u', '??' => 'y', '??' => 'b', '??' => 'y'];
    return strtr($string, $unwanted_array);
}
