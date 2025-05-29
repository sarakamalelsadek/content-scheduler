<?php

namespace App\Http\Requests;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

abstract class BaseFormRequest extends FormRequest
{

    use ApiResponseTrait;
    public function rules() :array
    {
        $this->sanitizeRequest();
        return $this->all();
    }

    abstract public function authorize() :bool;

    protected function failedValidation($validator)
    {
        $response = $this->_sendResponse
        (
            false,
            self::STATUS_BAD_REQUEST,
            'Validation error',
            $validator->errors()
        );
        throw new HttpResponseException($response);
    }

    public function messages() :array
    {
        return [
            'required' => __('validation.required'),
            'string' => __('validation.string'),
            'max' => [
                'string' => __('validation.max.string'),
                'integer' => __('validation.max.integer'),
            ],
            'date' => __('validation.date'),
            'date_format' => __('validation.date_format'),
            'in' => __('validation.in'),
            'email' => __('validation.email'),
            'unique' => __('validation.unique'),
            'unique_soft' => __('validation.unique'),
            'unique_encrypted' => __('validation.unique'),
            'unique_encrypted_soft' => __('validation.unique'),
            'regex' => __('validation.regex'),
            'min' => [
                'string' => __('validation.min.string'),
                'integer' => __('validation.min.integer'),
            ],
            'digits' => __('validation.digits'),
            'required_if' => __('validation.required'),
            'images.max' => __('validation.fields.upload_count_limit'),
            'disclaimer' => __('messages.property.disclaimer_not_accepted_error'),
            // Add more messages as needed...
        ];
    }

    // to sanitize data request coming from frontend
    private function sanitizeRequest(): void
    {
        $sanitizedData = $this->sanitizeData($this->all());
        $this->merge($sanitizedData);
    }

    private function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            // convert undefined value or null value as string to Null
            if (is_string($value) && (strtolower($value) === 'undefined' || strtolower($value) === 'null')) {
                $data[$key] = null;
            }
        }
        return $data;
    }

}


