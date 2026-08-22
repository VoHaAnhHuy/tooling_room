<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id'         => ['required', 'string', 'max:100', 'unique:tickets,ticket_id'],
            'ticket_type'       => ['required', 'string', 'max:20'],
            'related_ticket_id' => ['nullable', 'string', 'max:100', 'exists:tickets,ticket_id'],
            'product_id'        => ['nullable', 'string', 'max:50', 'exists:products,product_id'],
            'link_name'         => ['required', 'string', 'max:100'],
            'requester_name'    => ['required', 'string', 'max:100'],
            'issuer_name'       => ['required', 'string', 'max:100'],
            'status'            => ['nullable', 'string', 'max:20'],
        ];
    }
}
