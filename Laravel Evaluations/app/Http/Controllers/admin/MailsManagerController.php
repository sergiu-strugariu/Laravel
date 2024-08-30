<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Repositories\MailsRepository;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/**
 * Class MailsManagerController
 * @package App\Http\Controllers\admin
 */
class MailsManagerController extends Controller
{
    /**
     * @var $userRepository
     */
    private $mailsRepository;

    /**
     * MailsManagerController constructor.
     *
     * @param MailsRepository $mailsRepository
     */
    public function __construct(MailsRepository $mailsRepository)
    {
        $this->mailsRepository = $mailsRepository;
    }

    /**
     *  Listing page
     *
     * @return mixed
     */
    public function index()
    {
        $emailTemplates = $this->mailsRepository->getAll();
        return view('mails.index', compact('emailTemplates'));
    }

    /**
     * Ajax
     *
     * @param $id
     * @return mixed
     */
    public function getMail($id)
    {
        $mailTemplate = $this->mailsRepository->getById($id);

        return view('mails.partials.modal-template', compact('mailTemplate'));
    }


    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateMail($id, Request $request){

        $rules = [
            'name' => 'required',
            'body_en' => 'required',
            'body_ro' => 'required',
            'subject' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return ajaxResponse(ERROR, $validator->messages());
        }

        $this->mailsRepository->update($id, $request->only(array_keys($rules)));
        $template = $this->mailsRepository->getById($id);
        
        return ajaxResponse(SUCCESS, null, $template);
    }

}