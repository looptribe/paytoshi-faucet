<?php

namespace Looptribe\Paytoshi\Model;


class PayoutMapper
{
    public function toArray(Payout $model)
    {
        $data = array();
        $data['id'] = $model->getId();
        $data['recipient_address'] = $model->getRecipientAddress();
        $data['referral_recipient_address'] = $model->getReferralRecipientAddress();
        $data['earning'] = $model->getEarning();
        $data['referral_earning'] = $model->getReferralEarning();
        $data['created_at'] = $model->getCreatedAt();
        return $data;
    }

    public function toModel(array $data)
    {
        $payout = new Payout();
        if (array_key_exists('id', $data))
            $payout->setId($data['id']);
        if (array_key_exists('recipient_address', $data))
            $payout->setRecipientAddress($data['recipient_address']);
        if (array_key_exists('referral_recipient_address', $data))
            $payout->setReferralRecipientAddress($data['referral_recipient_address']);
        if (array_key_exists('earning', $data))
            $payout->setEarning($data['earning']);
        if (array_key_exists('referral_earning', $data))
            $payout->setReferralEarning($data['referral_earning']);
        if (array_key_exists('created_at', $data))
            $payout->setCreatedAt($data['created_at']);
        return $payout;
    }
}