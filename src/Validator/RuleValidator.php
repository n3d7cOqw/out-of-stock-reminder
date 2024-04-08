<?php

namespace OutOfStockReminder\Validator;

use OutOfStockReminder\Form\RuleType;
use Symfony\Component\HttpFoundation\Request;

class RuleValidator
{
    public static function isValidTitle(string $title): bool{
        return strlen(trim($title) > 3);
    }


    public static function isValidThreshold(string $threshold): bool{
        return strlen(trim($threshold) > 3) && intval($threshold) > 0;
    }

    public static function isValidEmails(string $emails): bool{
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

    public static function isValidStatus(int $status):bool{
        return ($status === 1 || $status === 0);
    }

    public static function isValidForm($form,Request $request):bool{
        return self::isValidTitle($form->get('title')->getData())
            && self::isValidThreshold($form->get('threshold')->getData())
            && self::isValidStatus($request->request->get("status"))
            && self::isValidEmails($form->get('email')->getData());
    }

    public static function issetProduct(Request $request): bool
    {
        return $request->request->get("rule")["product"] !== "";
    }

    public static function isOneRule(Request $request):bool
    {
        return intval($request->request->get("rule")["product"] !== "")
            + intval(isset($request->request->get("rule")["category_id"]) + intval($request->request->has("category_id"))) === 1;
    }

    


}