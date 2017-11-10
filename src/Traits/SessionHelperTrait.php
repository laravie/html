<?php

namespace Collective\Html\Traits;

use Illuminate\Contracts\Session\Session;

trait SessionHelperTrait
{
    /**
     * The CSRF token used by the form builder.
     *
     * @var string
     */
    protected $csrfToken;

    /**
     * The session store implementation.
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * Get a value from the session's old input.
     *
     * @param  string  $name
     *
     * @return string
     */
    public function old($name)
    {
        if (! isset($this->session)) {
            return;
        }

        $key     = $this->transformKey($name);
        $payload = $this->session->getOldInput($key);

        if (! is_array($payload)) {
            return $payload;
        }

        if (! in_array($this->type, ['select', 'checkbox'])) {
            if (! isset($this->payload[$key])) {
                $this->payload[$key] = collect($payload);
            }

            if (! empty($this->payload[$key])) {
                $value = $this->payload[$key]->shift();

                return $value;
            }
        }

        return $payload;
    }

    /**
     * Determine if the old input is empty.
     *
     * @return bool
     */
    public function oldInputIsEmpty()
    {
        return isset($this->session) && count($this->session->getOldInput()) == 0;
    }

    /**
     * Get the session store implementation.
     *
     * @return  \Illuminate\Contracts\Session\Session
     */
    public function getSessionStore()
    {
        return $this->session;
    }

    /**
     * Set the session store implementation.
     *
     * @param  \Illuminate\Contracts\Session\Session  $session
     *
     * @return $this
     */
    public function setSessionStore(Session $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return string
     */
    abstract public function token();

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    abstract protected function transformKey($key);
}
