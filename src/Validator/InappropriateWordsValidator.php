<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class InappropriateWordsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var InappropriateWords $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here

        $value = strtolower($value);
        foreach($constraint->listWords as $inappropriateWord){
            if(str_contains($value, $inappropriateWord)){
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ inappropriateWord }}', $inappropriateWord)
            ->addViolation();
            }
        }
    }
}