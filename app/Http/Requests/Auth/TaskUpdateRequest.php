<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;

class TaskUpdateRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:500',
            'description' => 'required',
            'status' => 'required',
            'due_date' => 'required',
            'assignees' => 'required'
        ];
    }
}
