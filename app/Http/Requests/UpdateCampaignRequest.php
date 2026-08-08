<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:campaign_categories,id'],
            'description' => ['required', 'string'],
            'goal_amount' => ['required', 'numeric', 'min:1000'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:20'],
            'gallery_images.*' => ['image', 'max:5120'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', 'exists:campaign_media,id'],
            'thank_you_message' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'submit_for_approval' => ['nullable', 'boolean'],
            'submission_comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
