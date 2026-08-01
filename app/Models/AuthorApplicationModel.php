<?php

namespace App\Models;

class AuthorApplicationModel extends BaseModel
{
    protected $table = 'author_applications';
    protected $allowedFields = ['user_id', 'status', 'portfolio_url', 'about_expertise', 'reviewer_feedback', 'updated_at', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // STATUS CONSTANTS
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SOFT_REJECT = 'soft_reject';
    public const STATUS_HARD_REJECT = 'hard_reject';

    /**
     * Retrieves the application of a specific user
     */
    public function getApplicationByUserId(int $userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Submits a new application or updates an existing soft-rejected one
     */
    public function submitApplication(int $userId, string $portfolioUrl, string $aboutExpertise): bool
    {
        $existingApp = $this->getApplicationByUserId($userId);

        $data = [
            'user_id'         => $userId,
            'portfolio_url'   => $portfolioUrl,
            'about_expertise' => $aboutExpertise,
            'status'          => self::STATUS_PENDING,
            'reviewer_feedback'  => ''
        ];

        // If a record already exists for this user
        if ($existingApp) {
            // Only allow updates if the application was soft-rejected
            if ($existingApp->status === self::STATUS_SOFT_REJECT) {
                return $this->update($existingApp->id, $data);
            }

            return false;
        }

        // If no application exists, create the initial record
        return (bool)$this->insert($data);
    }

    /**
     * Updates the status of an application
     */
    public function updateApplicationStatus(int $applicationId, string $status, ?string $reviewerFeedback = null): bool
    {
        $data = [
            'status'         => $status,
            'reviewer_feedback' => $reviewerFeedback
        ];

        return (bool)$this->update($applicationId, $data);
    }

    /**
     * Finds and paginates all author applications
     */
    public function findAllPaginated(int $perPage): array
    {
        return $this->select('author_applications.*, s.username, s.slug, s.first_name, s.last_name, s.email, s.avatar, s.storage_avatar')
            ->join('users s', 's.id = author_applications.user_id')
            ->orderBy("CASE WHEN author_applications.status = '" . self::STATUS_PENDING . "' THEN 1 ELSE 2 END", "ASC")
            ->orderBy('author_applications.id', 'DESC')
            ->paginate($perPage);
    }
}