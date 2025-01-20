<?php
require_once('../initialize.php');
require_once('../src/flutterwave_config.php');

// Get the event payload from Flutterwave
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Check if it's the correct event type
if (isset($data['event'])) {
    $flutterwave = new Flutterwave(FLW_SECRET_KEY);
    $payment = new Payment();

    switch ($data['event']) {
         // Handle successful payment
         case 'charge.completed':        
            $transaction_id = $data['data']['id'];
            $status = $data['data']['status'];
            $tx_ref = $data['data']['tx_ref'];
            // Verify the transaction status
            $transactionData = $flutterwave->verifyTransaction($transaction_id);

            if ($transactionData && $transactionData['status'] === 'successful') {
                // Update the payment record in the database to "successful"
                $payment::updatePaymentStatus($tx_ref, 'succesful');

                $user_id = $transactionData['meta']['user_id'];
                $training_id = $transactionData['meta']['training_id'];

                // Grant access to the training
                $user = Users::findById($user_id);
                $training_access = new User_Training_Access();
                if ($user) {
                    if ($training_access->addTrainingAccess($user_id, $payment->TRAINING_ID)) {
                        echo json_encode(['status' => 'success', 'message' => 'Training access granted']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to grant training access']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'User not found']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Transaction verification failed']);
            }
            break;

        // Handle cancelled payment
        case 'charge.cancelled':
            $tx_ref = $data['data']['tx_ref'];

            // Update the payment record to "cancelled"
            $payment->updatePaymentStatus($tx_ref, 'cancelled');
            echo json_encode(['status' => 'cancelled', 'message' => 'Payment was cancelled by the user']);
            break;

        // Handle any other events you want to capture
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid event type']);
            break;
    }

    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid event payload']);
}
?>
