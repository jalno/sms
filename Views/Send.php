<?php

namespace packages\sms\Views;

class Send extends Form
{
    public function setNumbers($numbers)
    {
        $this->setData($numbers, 'numbers');
    }

    protected function getNumbers()
    {
        return $this->getData('numbers');
    }
}
