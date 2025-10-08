<?php

namespace Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'package_name' => 'required|string|max:255',
            'user_type' => 'required|in:customer,driver,merchant',
            'price' => 'required|numeric|min:0',
            'validity' => 'required|integer|min:1',
            'credits' => 'required|integer|min:-1',
            'members_included' => 'nullable|integer',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
