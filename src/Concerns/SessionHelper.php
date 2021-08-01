<?php

namespace Collective\Html\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property array  $payload
 */
trait SessionHelper
{
    /**
     * The CSRF token used by the form builder.
     *
     * @var string|null
     */
    protected $csrfToken;

    /**
     * The session store implementation.
     *
     * @var \Illuminate\Contracts\Session\Session|null
     */
    protected $session;

    /**
     * Get a value from the session's old input.
     *
     * @param  string  $name
     *
     * @return array|string|null
     */
    public function old(string $name)
    {
        if (! isset($this->session)) {
            return null;
        }

        $key     = $this->transformKey($name);
        $payload = $this->session->getOldInput($key);

        if (! \is_array($payload)) {
            return $payload;
        }

        if (! \in_array($this->type, ['select', 'checkbox'])) {
            if (! isset($this->payload[$key])) {
                $this->payload[$key] = Collection::make($payload);
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
    public function oldInputIsEmpty(): bool
    {
        return isset($this->session) && \count((array) $this->session->getOldInput()) === 0;
    }

    /**
     * Get the session store implementation.
     *
     * @return  \Illuminate\Contracts\Session\Session|null
     */
    public function getSessionStore(): ?Session
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
    public function setSessionStore(Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    abstract public function token(): Htmlable;

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    abstract protected function transformKey(string $key): string;
}
