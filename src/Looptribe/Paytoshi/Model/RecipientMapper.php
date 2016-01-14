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
        $data['created_at'] = $model->getCreatedAt()->format('Y-m-d H:i:s');
        $data['updated_at'] = $model->getUpdatedAt()->format('Y-m-d H:i:s');
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
            $recipient->setCreatedAt(new \DateTime($data['created_at']));
        else
            $recipient->setCreatedAt(new \DateTime());
        if (array_key_exists('updated_at', $data))
            $recipient->setUpdatedAt(new \DateTime($data['updated_at']));
        else
            $recipient->setUpdatedAt(new \DateTime());
        return $recipient;
    }
}