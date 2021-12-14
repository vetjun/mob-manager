<?php


namespace App\Utils\Traits;

use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    /**
     * @param Request $request
     * @param array $rules
     * @throws ValidationException
     */
    public function validateRequest(Request $request, array $rules = [])
    {
        $all = $request->all();
        $validation = Validator::make($all,
            $rules ?? $this->getValidationRules()
        );
        if ($validation->fails()) {
            throw new ValidationException(implode('||', $validation->getMessageBag()->all()));
        }
    }
}
