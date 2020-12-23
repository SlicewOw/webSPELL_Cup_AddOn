<?php

namespace myrisk\Cup;

use webspell_ng\User;
use myrisk\Cup\Enum\SupportTicketEnums;


class SupportTicket {

    /**
     * @var int $ticket_id
     */
    private $ticket_id = null;

    /**
     * @var string $subject
     */
    private $subject;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var array<SupportTicketContent> $content
     */
    private $content = array();

    /**
     * @var \DateTime $start_date
     */
    private $start_date;

    /**
     * @var ?\DateTime $take_date
     */
    private $take_date = null;

    /**
     * @var ?\DateTime $close_date
     */
    private $close_date = null;

    /**
     * @var User $opened_by_user
     */
    private $opened_by_user;

    /**
     * @var ?User $closed_by_user
     */
    private $closed_by_user = null;

    /**
     * @var ?User $admin_id
     */
    private $admin_id = null;

    /**
     * @var int $ticket_status
     */
    private $ticket_status = SupportTicketEnums::TICKET_STATUS_OPEN;

    public function __construct()
    {
        $this->start_date = new \DateTime("now");
    }

    public function setTicketId(int $ticket_id): void
    {
        $this->ticket_id = $ticket_id;
    }

    public function getTicketId(): ?int
    {
        return $this->ticket_id;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param array<SupportTicketContent> $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array<SupportTicketContent>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function setStartDate(\DateTime $start_date): void
    {
        $this->start_date = $start_date;
    }

    public function getStartDate(): \DateTime
    {
        return $this->start_date;
    }

    public function setTakeDate(\DateTime $take_date): void
    {
        $this->take_date = $take_date;
    }

    public function getTakeDate(): ?\DateTime
    {
        if (is_null($this->take_date)) {
            return null;
        }
        return $this->take_date;
    }

    public function setCloseDate(\DateTime $close_date): void
    {
        $this->close_date = $close_date;
    }

    public function getCloseDate(): ?\DateTime
    {
        if (is_null($this->close_date)) {
            return null;
        }
        return $this->close_date;
    }

    public function setOpener(User $opener): void
    {
        $this->opened_by_user = $opener;
    }

    public function getOpener(): ?User
    {
        return $this->opened_by_user;
    }

    public function setCloser(User $closer): void
    {
        $this->closed_by_user = $closer;
    }

    public function getCloser(): ?User
    {
        return $this->closed_by_user;
    }

    public function setAdmin(User $admin_id): void
    {
        $this->admin_id = $admin_id;
    }

    public function getAdmin(): ?User
    {
        return $this->admin_id;
    }

    public function setStatus(int $ticket_status): void
    {
        $this->ticket_status = $ticket_status;
    }

    public function getStatus(): int
    {
        return $this->ticket_status;
    }

}
