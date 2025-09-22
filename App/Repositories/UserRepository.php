<?php

namespace App\Repositories;

use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\EmailConfirmation;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\Invitation;

/*use Illuminate\Support\Facades\Http;*/
use Illuminate\Support\Facades\Log; // Import Log facade at the top
use Illuminate\Support\Facades\Mail;

class UserRepository implements UserRepositoryInterface
{
    protected function logError($url, $error_message)
    {
        Log::error('Error in user repository function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function encryptText($text)
    {
        $encrypt_key = env('ENCRYPT_KEY');
        $encrypt_code = env('ENCRYPT_CODE');
        // Encrypt the text
        $encryptedText = openssl_encrypt($text, 'AES-128-CBC', $encrypt_key, OPENSSL_RAW_DATA, $encrypt_code);
        if ($encryptedText == '' || $encryptedText == null) {
            return $text;
        }
        // Return the encrypted text in Base64 encoding
        return base64_encode($encryptedText);
    }

    public function decryptText($encryptedText)
    {
        $encrypt_key = env('ENCRYPT_KEY');
        $encrypt_code = env('ENCRYPT_CODE');
        $text = $encryptedText;
        // Decode the text from Base64
        $encryptedText = base64_decode($encryptedText);
        // Decrypt the text
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $encrypt_key, OPENSSL_RAW_DATA, $encrypt_code);
        if ($decryptedText == '' || $decryptedText == null) {
            return $text;
        }
        return $decryptedText;
    }

    function generateRandomString($length/* = 4*/ , $type/* = 1*/)
    {
        if (intval($type) == 1) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else if (intval($type) == 2) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*+-/?><';
        } else if (intval($type) == 3) {
            $characters = '0123456789';
        } else if (intval($type) == 4) {
            $characters = '0123456789abcdefg';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function userSignUp(array $data)
    {
        try {
            $full_name = isset($data['full_name']) ? $data['full_name'] : null;
            $email_address = isset($data['email_address']) ? $data['email_address'] : null;
            $password = isset($data['password']) ? $data['password'] : null;
            $user_type_id = isset($data['user_type_id']) ? intval($data['user_type_id']) : 3;
            $is_active = isset($data['is_active']) ? boolval($data['is_active']) : 1;
            $mobile = isset($data['mobile_number']) ? $data['mobile_number'] : 00000000;

            $is_email_exist = User::where('email_address', $email_address)
                ->where('is_active', 1)
                ->orderBy('id', 'desc')->first();
            Log::info($is_email_exist);
            if ($is_email_exist) {
                $output['success'] = false;
                $output['message'] = "The email address you've entered is already associated with an existing account!";
                $output['data'] = null;
            } else {
                $date_time = date('Y-m-d H:i:s');

                $new_user = User::create([
                    'user_type_id' => $user_type_id,
                    'full_name' => $full_name,
                    'email_address' => $email_address,
                    'mobile_number' => $mobile,
                    'password' => Hash::make($password),
                    'is_active' => $is_active,
                    'created_at' => $date_time,
                    'updated_at' => $date_time
                ]);

                // Generate JWT token
                $token = JWTAuth::fromUser($new_user);

                $output['success'] = true;
                $output['message'] = "User sign up success";
                $output['data']['user_id'] = isset($new_user->id) ? intval($new_user->id) : 0;
                $output['data']['full_name'] = isset($new_user->full_name) ? $new_user->full_name : null;
                $output['data']['email_address'] = isset($new_user->email_address) ? $new_user->email_address : null;
                $output['data']['token'] = $token;
            }
        } catch (\Exception $e) {
            $url = isset($data['url']) ? $data['url'] : null;
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }
        return $output;
    }

    public function userSignIn(array $data)
    {
        try {
            Log::info("message :", $data);
            //$mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : null;
            // $mobile_number = isset($data['mobile_number']) ? trim($data['mobile_number']) : null;
            $email_address = isset($data['email_address']) ? trim($data['email_address']) : null;
            $password = isset($data['password']) ? $this->decryptText($data['password']) : null;
            //$user_type_id = isset($data['user_type_id']) ? intval($data['user_type_id']) : 0;
            $push_id = isset($data['push_id']) ? $data['push_id'] : null;
            $os_type = isset($data['os_type']) ? intval($data['os_type']) : 0;



            $user = User::where('email_address', $email_address)
                ->where('is_active', 1)
                ->orderBy('id', 'desc')->first();

            if (!isset($user->id)) {
                $output['success'] = false;
                $output['message'] = "Account does not exist.";
                $output['data'] = null;
            } else {
                if (true) {
                    /*$sys_type_id = isset($user->user_type_id) ? intval($user->user_type_id) : 0;
                    if($user_type_id == $sys_type_id) {*/
                    $credentials['email_address'] = $email_address;
                    $date_time = date('Y-m-d H:i:s');
                    $user->password = $password; // Use social password for social login
                    $user->updated_at = $date_time;
                    $user->save();
                    $credentials['password'] = $password; // Use social password for social login
                    if (!$token = JWTAuth::attempt($credentials)) {
                        $output['success'] = false;
                        $output['message'] = "Invalid credentials-Email. Please check & try again.";
                        $output['data'] = null;
                    } else {
                        $output['success'] = true;
                        $output['message'] = "User sign in success.";
                        $output['data']['user_id'] = isset($user->id) ? intval($user->id) : 0;
                        $output['data']['full_name'] = isset($user->full_name) ? $user->full_name : null;
                        $output['data']['email_address'] = isset($user->email_address) ? $user->email_address : null;
                        $output['data']['token'] = $token;
                    }
                }
            }
        } catch (\Exception $e) {
            $url = isset($data['url']) ? $data['url'] : null;
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }
        return $output;
    }

    public function userValidate($data)
    {
        try {
            $email_address = isset($data['email_address']) ? $data['email_address'] : null;
            $user = User::where('email_address', $email_address)
                ->where('is_active', 1)
                ->orderBy('id', 'desc')->first();


            if (isset($user->id)) {
                $output['success'] = true;
                $output['message'] = "User verification success.";
                $output['data']['user_id'] = isset($user->id) ? intval($user->id) : 0;
                $output['data']['full_name'] = isset($user->full_name) ? $user->full_name : null;
                $output['data']['email_address'] = isset($user->email_address) ? $user->email_address : null;
                $output['data']['nic_number'] = isset($user->nic_number) ? $user->nic_number : null;
                $output['data']['address_line1'] = isset($user->address_line1) ? $user->address_line1 : null;
                $output['data']['address_line2'] = isset($user->address_line2) ? $user->address_line2 : null;
                $output['data']['os_type'] = isset($user->os_type) ? intval($user->os_type) : 0;
                $output['data']['is_active'] = isset($user->is_active) ? intval($user->is_active) : 0;
                $output['data']['push_id'] = isset($user->push_id) ? $user->push_id : null;
                // $chamber_id = isset($user->chamber_id) ? intval($user->chamber_id) : 0;
                // $output['data']['chamber_id'] = $chamber_id;
                // $output['data']['is_chamber_setup_done'] = isset($user->is_chamber_setup_done) ? intval($user->is_chamber_setup_done) : 0;
                // $output['data']['profile_image'] = isset($user->profile_image) ? $user->profile_image : null;

                // $chamber_data = DB::table('chambers AS c')
                //                     ->join('chamber_complexs AS cc', 'cc.id', 'c.chamber_complex_id')
                //                     ->select('c.chamber_name', 'c.chamber_complex_id', 'cc.chamber_complex')
                //                     ->where('c.is_active', 1)->where('c.id', $chamber_id)
                //                     ->orderBy('c.id', 'DESC')->first();

                // $output['data']['chamber_name'] = isset($chamber_data->chamber_name) ? $chamber_data->chamber_name : null;
                // $output['data']['chamber_complex_id'] = isset($chamber_data->chamber_complex_id) ? intval($chamber_data->chamber_complex_id) : 0;
                // $output['data']['chamber_complex'] = isset($chamber_data->chamber_complex) ? $chamber_data->chamber_complex : null;
            } else {
                $output['success'] = false;
                $output['message'] = "User does not exist.";
                $output['data'] = null;
            }
        } catch (\Exception $e) {
            $url = "auth/validate";
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }
        return $output;
    }

    public function getAllUsers($data)
    {
        try {
            $perPage = (int) request()->get('perPage', 10);
            $mobileNumber = request()->get('mobile_number');

            $query = User::query();
            $query->where('is_active', 1);
            $query->where('bank_id', $data);

            if ($mobileNumber) {
                $query->where('mobile_number', 'LIKE', "%{$mobileNumber}%");
            }

            $users = $query->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Users retrieved successfully',
                'pagination' => [
                    'perPage' => $users->perPage(),
                    'total' => $users->total(),
                    'currentPage' => $users->currentPage(),
                    'lastPage' => $users->lastPage(),
                    'hasMore' => $users->hasMorePages()
                ],
                'data' => $users->items(),
                'filter' => [
                    'mobile_number' => $mobileNumber
                ]
            ];
        } catch (\Exception $e) {
            $url = "auth/users";
            $this->logError($url, $e->getMessage());

            return [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null,
                'pagination' => null,
                'filter' => null
            ];
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            // Soft delete by updating isActive to 0
            $user->is_active = 0;
            $user->save();

            return [
                'success' => true,
                'message' => 'User deactivated successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            $url = "auth/users/delete";
            $this->logError($url, $e->getMessage());

            return [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function updateUser($id, $data)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            if (isset($data['full_name'])) {
                $user->full_name = $data['full_name'];
            }
            if (isset($data['email_address'])) {
                $existingUser = User::where('email_address', $data['email_address'])
                    ->where('id', '!=', $id)
                    ->where('is_active', 1)
                    ->first();

                if ($existingUser) {
                    return [
                        'success' => false,
                        'message' => 'Email address already exists',
                        'data' => null
                    ];
                }
                $user->email_address = $data['email_address'];
            }
            if (isset($data['mobile_number'])) {
                $existingUser = User::where('mobile_number', $data['mobile_number'])
                    ->where('id', '!=', $id)
                    ->where('is_active', 1)
                    ->first();

                if ($existingUser) {
                    return [
                        'success' => false,
                        'message' => 'Mobile number already exists',
                        'data' => null
                    ];
                }
                $user->mobile_number = $data['mobile_number'];
            }
            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if (isset($data['user_type_id'])) {
                $user->user_type_id = $data['user_type_id'];
            }
            if (isset($data['nic_number'])) {
                $user->nic_number = $data['nic_number'];
            }
            if (isset($data['address_line1'])) {
                $user->address_line1 = $data['address_line1'];
            }
            if (isset($data['address_line2'])) {
                $user->address_line2 = $data['address_line2'];
            }
            if (isset($data['city_id'])) {
                $user->city_id = $data['city_id'];
            }
            if (isset($data['district_id'])) {
                $user->district_id = $data['district_id'];
            }
            if (isset($data['province_id'])) {
                $user->province_id = $data['province_id'];
            }
            if (isset($data['bank_id'])) {
                $user->bank_id = $data['bank_id'];
            }
            $user->is_active = 1;

            $user->save();
            $userData = $user->toArray();
            unset($userData['password']);
            unset($userData['normal_password']);
            unset($userData['social_password']);

            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $userData
            ];
        } catch (\Exception $e) {
            $url = "auth/users/update";
            $this->logError($url, $e->getMessage());

            return [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function userData($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            $userData = $user->toArray();
            unset($userData['password']);

            return [
                'success' => true,
                'message' => 'User data fetched successfully',
                'data' => $userData
            ];
        } catch (\Exception $e) {
            $url = "auth/users/data";
            $this->logError($url, $e->getMessage());

            return [
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage(),
                'data' => null
            ];
        }

    }

    public function saveInvitation($data)
    {
        try {
            $user = User::find($data['user_id']);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            $existinInvitation = Invitation::where('email_address', $data['email_address'])
                ->where('is_active', 1)->first();
            if ($existinInvitation && $existinInvitation->created_at > date('Y-m-d H:i:s', strtotime('-2 minutes'))) {
                return [
                    'success' => false,
                    'message' => 'User already has an invitation code',
                    'data' => null
                ];
            }

            $invitation = Invitation::create($data);
            return [
                'success' => true,
                'message' => 'Invitation saved successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            $url = "auth/users/saveInvitation";
            $this->logError($url, $e->getMessage());
        }
    }
    public function getInvitation($data)
    {
        try {
            $existinInvitation = Invitation::where('token', $data)
                ->where('is_active', 1)->first();

            if (!$existinInvitation) {
                return [
                    'success' => false,
                    'message' => 'Invalid token',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Invitation found successfully',
                'data' => $existinInvitation
            ];
        } catch (\Exception $e) {
            $url = "auth/users/saveInvitation";
            $this->logError($url, $e->getMessage());
        }
    }
    public function updateInvitation($id)
    {
        try {
            $invitation = Invitation::find($id);

            $invitation->is_active = 0;
            $invitation->save();
            return [
                'success' => true,
                'message' => 'Invitation updated successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            $url = "auth/users/saveInvitation";
            $this->logError($url, $e->getMessage());
        }
    }
    public function resetPassword($data)
    {
        try {
            $user = User::where('email_address', $data['email_address'])
                ->where('is_active', 1)->first();
            ;

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            $user->password = Hash::make($data['password']);
            $user->save();
            return [
                'success' => true,
                'message' => 'Password updated successfully',
                'data' => null
            ];
        } catch (\Exception $e) {
            $url = "auth/users/saveInvitation";
            $this->logError($url, $e->getMessage());
        }
    }
    public function getUserByEmail($email)
    {
        try {
            $user = User::where(column: 'email_address', operator: $email)
                ->where(column: 'is_active', operator: 1)->first();
            ;

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'User found',
                'data' => $user
            ];
        } catch (\Exception $e) {
            $url = "auth/users/saveInvitation";
            $this->logError(url: $url, error_message: $e->getMessage());
        }
    }
    public function saveOtp(array $data): array
    {
        // Delete old OTPs for this email
        Otp::where(column: 'email', operator: $data['email'])->delete();

        Otp::create(attributes: $data);

        return [
            'success' => true,
            'message' => 'OTP saved successfully',
            'data' => null
        ];
    }

    public function getOtp($email, $otp): Otp|null
    {
        return Otp::where(column: 'email', operator: $email)
            ->where(column: 'otp', operator: $otp)
            ->first();
    }

    public function deleteOtp($id): bool|null
    {
        return Otp::where(column: 'id', operator: $id)->delete();
    }

    public function updatePassword(string $id, string $currentPassword, string $newPassword): array
    {
        try {
            $user = User::where(column: 'id', operator: $id)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'data' => null
                ];
            }

            // check current password
            if (!Hash::check(value: $currentPassword, hashedValue: $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'data' => null
                ];
            }

            // update with new password
            $user->password = Hash::make(value: $newPassword);
            $user->save();

            return [
                'success' => true,
                'message' => 'Password updated successfully',
                'data' => $user
            ];
        } catch (\Exception $e) {
            \Log::error('Password update failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Something went wrong, please try again',
                'data' => null
            ];
        }
    }
}
