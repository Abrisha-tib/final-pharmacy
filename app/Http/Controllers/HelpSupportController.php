<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Help & Support Controller
 * 
 * Handles help documentation, support tickets, and user assistance.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class HelpSupportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display help and support page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get help sections
        $helpSections = $this->getHelpSections();
        
        return view('help-support.index', compact('user', 'helpSections'));
    }

    /**
     * Display FAQ page.
     */
    public function faq()
    {
        $faqs = $this->getFAQs();
        
        return view('help-support.faq', compact('faqs'));
    }

    /**
     * Display documentation page.
     */
    public function documentation()
    {
        $documentation = $this->getDocumentation();
        
        return view('help-support.documentation', compact('documentation'));
    }

    /**
     * Display contact support page.
     */
    public function contact()
    {
        return view('help-support.contact');
    }

    /**
     * Submit support request.
     */
    public function submitSupport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical,billing,feature_request,bug_report,general',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string|max:5000',
            'attachments' => 'array|max:3',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Create support ticket (you can create a SupportTicket model)
            $ticketData = [
                'user_id' => $user->id,
                'subject' => $request->subject,
                'category' => $request->category,
                'priority' => $request->priority,
                'message' => $request->message,
                'status' => 'open',
                'created_at' => now()
            ];

            // For now, we'll just log it (you can create a proper tickets table)
            \Log::info('Support Ticket Created', $ticketData);

            // Send email notification (optional)
            // Mail::to('support@yourcompany.com')->send(new SupportTicketMail($ticketData));

            return response()->json([
                'success' => true,
                'message' => 'Support request submitted successfully. We will get back to you within 24 hours.',
                'ticket_id' => 'TICKET-' . time()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit support request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get help sections.
     */
    private function getHelpSections()
    {
        return [
            [
                'title' => 'Getting Started',
                'icon' => 'fas fa-rocket',
                'description' => 'Learn the basics of using the pharmacy management system',
                'items' => [
                    'Dashboard Overview',
                    'Adding Your First Medicine',
                    'Creating a Sale',
                    'Managing Inventory'
                ]
            ],
            [
                'title' => 'Sales Management',
                'icon' => 'fas fa-shopping-cart',
                'description' => 'Everything about managing sales and transactions',
                'items' => [
                    'Creating New Sales',
                    'Processing Payments',
                    'Generating Receipts',
                    'Sales Reports'
                ]
            ],
            [
                'title' => 'Inventory Management',
                'icon' => 'fas fa-boxes',
                'description' => 'Managing your pharmacy inventory effectively',
                'items' => [
                    'Adding Medicines',
                    'Stock Management',
                    'Low Stock Alerts',
                    'Expiry Tracking'
                ]
            ],
            [
                'title' => 'Reports & Analytics',
                'icon' => 'fas fa-chart-bar',
                'description' => 'Understanding your business performance',
                'items' => [
                    'Sales Reports',
                    'Inventory Reports',
                    'Customer Analytics',
                    'Financial Reports'
                ]
            ],
            [
                'title' => 'User Management',
                'icon' => 'fas fa-users',
                'description' => 'Managing users and permissions',
                'items' => [
                    'Adding Users',
                    'Role Management',
                    'Permission Settings',
                    'User Activity'
                ]
            ],
            [
                'title' => 'System Settings',
                'icon' => 'fas fa-cog',
                'description' => 'Configuring system preferences',
                'items' => [
                    'General Settings',
                    'Notification Preferences',
                    'Backup & Security',
                    'System Maintenance'
                ]
            ]
        ];
    }

    /**
     * Get frequently asked questions.
     */
    private function getFAQs()
    {
        return [
            [
                'question' => 'How do I add a new medicine to the inventory?',
                'answer' => 'Go to the Medicines section, click "Add New Medicine", fill in the required information including name, category, strength, price, and stock quantity, then save.',
                'category' => 'Inventory'
            ],
            [
                'question' => 'How do I process a sale?',
                'answer' => 'Navigate to the Sales section, click "New Sale", search for medicines, add them to the cart, enter customer details, and complete the transaction.',
                'category' => 'Sales'
            ],
            [
                'question' => 'How do I generate reports?',
                'answer' => 'Go to the Reports section, select the type of report you need (Sales, Inventory, Financial), choose your date range, and click Generate Report.',
                'category' => 'Reports'
            ],
            [
                'question' => 'How do I set up low stock alerts?',
                'answer' => 'In the Medicine settings, set the reorder level for each medicine. The system will automatically alert you when stock falls below this level.',
                'category' => 'Inventory'
            ],
            [
                'question' => 'How do I backup my data?',
                'answer' => 'Go to Settings > System Settings > Backup. You can schedule automatic backups or create manual backups of your data.',
                'category' => 'System'
            ],
            [
                'question' => 'How do I add new users?',
                'answer' => 'Go to User Management, click "Add User", fill in user details, assign roles and permissions, then save.',
                'category' => 'Users'
            ],
            [
                'question' => 'How do I change my password?',
                'answer' => 'Click on your profile picture, select "Edit Profile", then click "Change Password" and follow the instructions.',
                'category' => 'Account'
            ],
            [
                'question' => 'How do I customize the dashboard?',
                'answer' => 'Go to your profile settings, select "Preferences", and configure which widgets you want to see on your dashboard.',
                'category' => 'Dashboard'
            ]
        ];
    }

    /**
     * Get documentation sections.
     */
    private function getDocumentation()
    {
        return [
            [
                'title' => 'User Guide',
                'description' => 'Complete guide for all users',
                'sections' => [
                    'Getting Started',
                    'Dashboard Overview',
                    'Navigation Guide',
                    'Basic Operations'
                ]
            ],
            [
                'title' => 'Administrator Guide',
                'description' => 'Guide for system administrators',
                'sections' => [
                    'User Management',
                    'System Configuration',
                    'Security Settings',
                    'Backup & Recovery'
                ]
            ],
            [
                'title' => 'API Documentation',
                'description' => 'Technical documentation for developers',
                'sections' => [
                    'Authentication',
                    'Endpoints',
                    'Response Formats',
                    'Error Handling'
                ]
            ],
            [
                'title' => 'Troubleshooting',
                'description' => 'Common issues and solutions',
                'sections' => [
                    'Login Issues',
                    'Performance Problems',
                    'Data Issues',
                    'Browser Compatibility'
                ]
            ]
        ];
    }
}
