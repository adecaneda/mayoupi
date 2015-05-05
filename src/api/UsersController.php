<?php

namespace api;

use Domain\EntityCollection;
use Domain\Repository\Users;
use Domain\Services\Auth;
use Library\Controller;

class UsersController extends Controller{

    public function index()
    {
        if (!Auth::isAdmin()) {
            $this->json(array('error' => 1));
            return;
        }

        /** @var $users EntityCollection */
        $users = Users::get()->retrieveAll();

        $this->json(array(
            'users' => $users->getAttrs(true, false),
            'totalCount' => count($users),
        ));
    }

    public function uploadAvatar()
    {
        if (!$user = Auth::authenticate('header')) {
            $this->json(array(
                'error' => 1,
            ));
            return;
        }

        if (empty($_FILES) || empty($_FILES['file'])) {
            $this->json(array(
                'error' => 2,
            ));
            return;
        }

        //@todo check type, size, etc..

        if (!$targeFileName = $this->moveUploadedAvatar($user)) {
            $this->json(array(
                'error' => 3,
            ));
        }

        $user->updateAvatar($targeFileName);

        $this->json(array(
            'user' => $user->getWithRelations(),
            'url' => $user->avatar()->get('url'),
        ));
    }

    public function acceptTerms()
    {
        if (!$user = Auth::authenticate('header')) {
            $this->json(array(
                'error' => 1,
            ));
            return;
        }

        $user->acceptTerms();

        Users::get()->persist($user);

        $this->json(array(
            'tac_accepted' => $user->get('tac_accepted'),
        ));
    }

    /**
     * @param $user
     *
     * @return string
     */
    protected function moveUploadedAvatar($user)
    {
        $file = $_FILES['file'];
        $parts = explode('.', basename($file['name']));
        $srcFileExt = end($parts);

        $targetDir = dirname($_SERVER['SCRIPT_FILENAME']) . '/users/assets/avatars/';
        $targeFileName = 'avatar_' . $user->getId() . '_' . time() . '.' . $srcFileExt;

        $targetFile = $targetDir . $targeFileName;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targeFileName;
        } else {
            return '';
        }
    }
}