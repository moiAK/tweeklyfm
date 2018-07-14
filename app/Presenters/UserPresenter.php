<?php namespace App\Presenters;

/**
 * Class UserPresenter
 *
 * @package App\Presenters
 */
class UserPresenter
{
    /**
     * @var
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        return $this->user;
    }
}
