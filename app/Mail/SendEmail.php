<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable 
{
    use Queueable, SerializesModels;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
	
	protected $data = [];
	protected $template;
	protected $email;
	protected $images;
    
	public function __construct($data)
	{
		$this->data = $data;
		$this->template = isset($this->data['template']) ? $this->data['template'] : 'email.default';
		$this->images = !empty($this->data['images']) ? $this->data['images'] : [];
		$this->email = isset($this->data['email']) ? $this->data['email'] : env('MAIL_USERNAME');
	}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	if ($this->images) {
			foreach ($this->images as $image) {
				$this->attach(base_path($image));
			}
		}
		
        return $this->from(env('MAIL_USERNAME'), $this->data['params']['name'])
			->subject($this->data['subject'])
			->to($this->email)
			->markdown($this->template)
			->with($this->data['params']);
    }
}
