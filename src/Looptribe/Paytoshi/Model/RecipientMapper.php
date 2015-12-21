<?php

namespace Looptribe\Paytoshi\Model;

class RecipientMapper
{
    public function toArray(Recipient $model)
    {
        $data = array();
        $data['id'] = $model->getId();
        $data['address'] = $model->getAddress();
        $data['earning'] = $model->getEarning();
        $data['referral_earning'] = $model->getReferralEarning();
        $data['created_at'] = $model->getCreatedAt();
        $data['updated_at'] = $model->getUpdatedAt();
        return $data;
    }

    public function toModel(array $data)
    {
        $recipient = new Recipient();
        if (array_key_exists('id', $data))
            $recipient->setId($data['id']);
        if (array_key_exists('address', $data))
            $recipient->setAddress($data['address']);
        if (array_key_exists('earning', $data))
            $recipient->setEarning($data['earning']);
        if (array_key_exists('referral_earning', $data))
            $recipient->setReferralEarning($data['referral_earning']);
        if (array_key_exists('created_at', $data))
            $recipient->setCreatedAt($data['created_at']);
        if (array_key_exists('updated_at', $data))
            $recipient->setUpdatedAt($data['updated_at']);
        return $recipient;
    }
}