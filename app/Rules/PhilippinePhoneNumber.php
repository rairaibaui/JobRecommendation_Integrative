<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhilippinePhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $value);

        // Philippine phone numbers should be 10 or 11 digits
        // 10 digits for mobile (e.g., 9171234567)
        // 11 digits for mobile with leading 0 (e.g., 09171234567)
        // 11 digits for mobile with country code 63 (e.g., 639171234567)

        if (!preg_match('/^(0?[9]\d{9})|(63[9]\d{9})$/', $cleaned)) {
            $fail('The :attribute must be a valid Philippine phone number.');
            return;
        }

        // Additional validation for common prefixes
        $validPrefixes = [
            '0905', '0906', '0907', '0908', '0909', // TNT
            '0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919', // Smart
            '0920', '0921', '0922', '0923', '0924', '0925', '0926', '0927', '0928', '0929', // Sun
            '0930', '0931', '0932', '0933', '0934', '0935', '0936', '0937', '0938', '0939', // TNT
            '0940', '0941', '0942', '0943', '0944', '0945', '0946', '0947', '0948', '0949', // Sun
            '0950', '0951', '0952', '0953', '0954', '0955', '0956', '0957', '0958', '0959', // Globe/TM
            '0960', '0961', '0962', '0963', '0964', '0965', '0966', '0967', '0968', '0969', // TNT
            '0970', '0971', '0972', '0973', '0974', '0975', '0976', '0977', '0978', '0979', // Globe
            '0980', '0981', '0982', '0983', '0984', '0985', '0986', '0987', '0988', '0989', // Smart
            '0990', '0991', '0992', '0993', '0994', '0995', '0996', '0997', '0998', '0999', // Dito
        ];

        // Extract the 4-digit prefix after removing country code if present
        $prefix = substr($cleaned, -10, 4);

        if (!in_array($prefix, $validPrefixes)) {
            $fail('The :attribute must be a valid Philippine mobile number with a recognized network prefix.');
        }
    }
}