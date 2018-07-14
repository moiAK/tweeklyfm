<?php namespace App\Presenters;

/**
 * Class UpdatePresenter
 *
 * @package App\Presenters
 */
class UpdatePresenter
{
    /**
     * @var
     */
    private $update;

    /**
     * @param Update $update
     */
    public function _construct(Update $update)
    {
    }

    /**
     * @return mixed
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param mixed $update
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    }
}
