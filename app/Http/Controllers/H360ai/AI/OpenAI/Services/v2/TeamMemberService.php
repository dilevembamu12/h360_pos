<?php 

namespace Modules\OpenAI\Services\v2;

use App\Models\{
    User,
    Team,
    TeamMemberMeta
};

use Exception;


class TeamMemberService
{
    /**
     * Create a new team based on the encrypted key and store the metadata.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    public function teamMemberStore($request, $id)
    {
        $requestEncryptedKey = $request->encryptedKey;
        $teamId = null;

        if ($requestEncryptedKey) {
            // Validate encrypted key
            if (!$this->isValidEncryptedKey($requestEncryptedKey)) {
                throw new Exception(__('Not a valid invitation link'));
            }

            // Decode the key and separate components
            [$decryptedId, $decryptedEmail] = explode('_', base64_decode($requestEncryptedKey));

            // Validate email
            if ($request['email'] !== $decryptedEmail) {
                throw new Exception(__('Not a valid Email Address'));
            }

            // Validate numeric ID
            if (!is_numeric($decryptedId)) {
                throw new Exception(__('Not a valid invitation link'));
            }

            // Extract parent user ID
            $parentId = $this->extractParentId($decryptedId);

            // Validate parent user
            $parentUser = User::find($parentId);
            if (!$parentUser) {
                throw new Exception(__('Not a valid invitation link'));
            }

            // Create a new team and insert metadata
            $teamArray = [
                'user_id'    => $id,
                'parent_id'  => $parentUser->id,
                'package_id' => 0,
                'status'     => 'Active',
            ];
            $teamId = (new Team)->store($teamArray);

            if ($teamId) {
                TeamMemberMeta::insertMetaData($teamId);
            }
        }

        return $teamId;
    }

    /**
     * Validates the given encrypted key.
     * 
     * @param string $encryptedKey Encrypted key
     * 
     * @return bool True if the key is valid
     */
    private function isValidEncryptedKey($encryptedKey) : bool
    {
        return $encryptedKey && base64_encode(base64_decode($encryptedKey, true)) === $encryptedKey;
    }

    /**
     * Extracts the parent ID from the decrypted ID.
     * 
     * @param string $decryptedId Decrypted ID
     * 
     * @return string Parent ID
     */
    private function extractParentId($decryptedId)
    {
        $substrFirstPart = substr($decryptedId, 3);
        return substr($substrFirstPart, 0, -4);
    }

    /**
     * Update team member metadata by the given type and usage.
     *
     * @param string $type Type of metadata (e.g., word, image, page).
     * @param int $usage The amount to increment the metadata by.
     * @param array $data Optional
     *
     * @return bool True if updated successfully, otherwise false.
     */
    public function updateTeamMeta(string $type, int $usage, array $data = []): bool
    {
        // Determine the user ID from authenticated user or provided data
        $userId = auth()->id() ?? ($data['owner_id'] ?? null);

        if (!$userId) {
            return false;
        }

        $memberData = Team::getMember($userId);

        if (!$memberData) {
            return false;
        }

        $meta = TeamMemberMeta::getMemberMeta($memberData->id, "{$type}_used");
        return $meta ? $meta->increment('value', $usage) : false;
    }

    /**
     * Check if the given user ID has access to the team member.
     * 
     * @param int $authUserId The ID of the authenticated user.
     * @param string $slug The slug to check access against.
     * 
     * @return bool True if the user has access, otherwise false.
     */
    public function hasTeamMemberAccess($authUserId, $slug): bool
    {
        $teamData = Team::where(['user_id' => $authUserId, 'status' => 'Active'])->first();
        
        if ($teamData) {
            $memberPackageData = TeamMemberMeta::getMemberMeta($teamData->id, 'packageUserId');
            $packageValue = $memberPackageData->value ?? '';
        
            if (subscription('isSubscribed', $authUserId) && $teamData->user_id == $packageValue) {
                return true;
            }
            $teamMeta = TeamMemberMeta::getMemberMeta($teamData->id, $slug);
            return ($teamMeta['value'] ?? 0) == 1;
        }

        return true;
    }
}
