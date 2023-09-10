<?php

namespace App\Http\Controllers\Banks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Bank\BankList;
use App\Models\Bank\BankDetail;

class BankController extends Controller
{
    //
    private $bankDetail;
    function __construct(BankDetail $bankDetail)
    {
        $this->bankDetail = $bankDetail;
    }
    public function fetchAllBanks()
    {
        $banks = BankList::where('status', $this->active)->get();
        if($banks->count() > 0){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Bank'), $banks);
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Bank'));
    }

    public function fetchSingleBank($code = null)
    {
        if($code != null){
            $bank = BankList::where('code', $code)->where('status', $this->active)->first();
            if($bank != null){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_single', 'Bank'), $bank);
            }
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Bank'));
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }

    public function verifyUserAccountNumber(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'account_number'  => 'required',
            'bank_code'  => 'required',
        ]);
        if($validator->fails()) return $this->returnMessageTemplate(false, "", $validator->errors());

        $response = $this->verifyAccountDetail($data['account_number'], $data['bank_code']);  
        if($response['status']){
            $bankDetails = $this->bankDetail->where('bank_id', $data['bank_code'])
                ->where('account_no', $data['account_number'])
                ->where('user_id', $this->user()->unique_id)
                ->first();
            if($bankDetails != null){
                $bankDetails->update([
                    'account_no' => $response['data']['account_number'],
                    'account_name' => $response['data']['account_name'],
                ]);
            }else{
                $this->bankDetail->create([
                    'unique_id' => $this->createUniqueId('bank_details'),
                    'user_id' => $this->user()->unique_id,
                    'bank_id' => $data['bank_code'],
                    'account_no' => $response['data']['account_number'],
                    'account_name' => $response['data']['account_name'],
                ]);
            }
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Account Number'), $response['data']);
        } 
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_accct_number'));
    }

    public function fetchUserBankDetails()
    {
        $userAccounts = $this->bankDetail->where('user_id', $this->user()->unique_id)->get();
        if($userAccounts->count() > 0){
            foreach($userAccounts as $userAccount)
            {
                $userAccount->bank;
            }
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Bank Details'), $userAccounts);
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Bank Details'));
    }
}
