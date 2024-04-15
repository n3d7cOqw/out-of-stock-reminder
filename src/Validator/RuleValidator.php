<?php

namespace OutOfStockReminder\Validator;

use OutOfStockReminder\Form\RuleType;
use Symfony\Component\HttpFoundation\Request;

class RuleValidator
{
    public static function isValidTitle($title): bool{
        return strlen(trim($title) ) > 3;
    }


    public static function isValidThreshold($threshold): bool{
        return $threshold > 0;
    }

    public static function isValidEmails($emails): bool{
        $emails = explode(" ", trim($emails));
        $isValid = true;
        foreach ($emails as $email){
            if (!preg_match('/^(([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/', $email)){
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    public static function isValidStatus($status):bool{
        return ($status === 1 || $status === 0 || $status === null);
    }

    public static function isValidForm($form):array{
        $errors = [];
        if (!self::isValidTitle($form->get('title')->getData())){
            $errors["title"] =  "Incorrect title. Title must be at least 4 symbols";
        }
        if (!self::isValidThreshold($form->get('threshold')->getData())){
            $errors["threshold"] =  "Incorrect threshold. Threshold must be greater that 0";
        }
        if (!self::isValidStatus($form->get('status')->getData())){
            $errors["status"] =  ["Incorrect status"];
        }
        if (!self::isValidEmails($form->get('email')->getData())){
            $errors["email"] =  "Incorrect email. Email must be like email@example.com";
        }
        if (count($errors) > 0){
            return $errors;
        }else{
            return [];
        }

    }

    public static function issetProduct(Request $request): bool
    {
        return $request->request->get("rule")["product"] !== "";
    }

    public static function isOneRule(Request $request):bool
    {
        return intval($request->request->get("rule")["product"] !== "")
            + intval(isset($request->request->get("rule")["category_id"]) + intval($request->request->get("rule")["select_all_categories"])) === 1;
    }

    


}