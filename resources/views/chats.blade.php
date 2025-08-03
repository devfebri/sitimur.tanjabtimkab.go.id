{{-- filepath: resources/views/chats.blade.php --}}
@extends('layouts.master')

@section('css')
    <style>
        /* Modern Government Chat Styling - Full Height */
        
        .page-content-wrapper {
            min-height: calc(100vh - 80px); /* Adjust based on header height */
            display: flex;
            flex-direction: column;
        }
        
        .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .row {
            flex: 1;
            display: flex;
        }
        
        .col-12 {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        #chat-component {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .govt-chat-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3c72 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }
        
        .chat-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-10px, -10px) rotate(360deg); }
        }
        
        .govt-badge {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #1e3c72;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .govt-time {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            backdrop-filter: blur(5px);
        }
        
        .chat-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .chat-subtitle {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }
        
        .breadcrumb-govt {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 8px 16px;
            backdrop-filter: blur(5px);
        }
        
        .breadcrumb-govt .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-govt .breadcrumb-item a:hover {
            color: #FFD700;
        }
        
        .breadcrumb-govt .breadcrumb-item.active {
            color: #FFD700;
            font-weight: 600;
        }
          @media (max-width: 768px) {
            .page-content-wrapper {
                min-height: calc(100vh - 60px);
            }
            
            .chat-header {
                padding: 20px 20px;
                text-align: center;
            }
            
            .chat-title {
                font-size: 1.4rem;
            }
            
            .govt-chat-container {
                margin: 5px;
                border-radius: 15px;
            }
            
            .chat-header {
                border-radius: 15px 15px 0 0;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .page-content-wrapper {
                padding: 5px 0;
                min-height: calc(100vh - 50px);
            }
            
            .container-fluid {
                padding: 0 5px;
            }
            
            .chat-header {
                padding: 15px;
            }
            
            .chat-title {
                font-size: 1.2rem;
            }
            
            .govt-chat-container {
                margin: 2px;
            }
        }
        
        /* Loading Animation */
        .govt-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 215, 0, 0.3);
            border-radius: 50%;
            border-top-color: #FFD700;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        
       <div class="row mt-2">
           <div class="col-12">
               <!-- Loading Indicator -->
               <div id="chat-loading" class="text-center py-4 d-none">
                   <div class="govt-loading me-2"></div>
                   <span class="text-muted">Memuat sistem komunikasi...</span>
               </div>               <!-- Chat Component -->
               <div id="chat-component">
                   @livewire('custom-chat', [
                       'pengajuanId' => request()->query('pengajuan'),
                       'withUserId' => request()->query('with_user')
                   ])
               </div>
           </div>
       </div>

        
    </div>
</div>
@endsection

@section('javascript')
    <script>
        console.log('🏛️ Government Chat System Loading...');
        
        // Show loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const loading = document.getElementById('chat-loading');
            const chatComponent = document.getElementById('chat-component');
            
            if (loading && chatComponent) {
                loading.classList.remove('d-none');
                chatComponent.style.opacity = '0';
                
                // Hide loading after component loads
                setTimeout(() => {
                    loading.classList.add('d-none');
                    chatComponent.style.opacity = '1';
                    chatComponent.style.transition = 'opacity 0.5s ease-in-out';
                }, 1500);
            }
        });
        
        // Livewire initialization
        document.addEventListener('livewire:initialized', () => {
            console.log('✅ Livewire Chat System Initialized');
            
            // Add smooth animations to chat interactions
            Livewire.on('message-sent', () => {
                // Add subtle animation feedback
                const chatComponent = document.getElementById('chat-component');
                if (chatComponent) {
                    chatComponent.style.transform = 'scale(1.01)';
                    setTimeout(() => {
                        chatComponent.style.transform = 'scale(1)';
                        chatComponent.style.transition = 'transform 0.2s ease';
                    }, 200);
                }
            });
        });
          // Responsive chat adjustments
        function adjustChatForMobile() {
            const isMobile = window.innerWidth <= 768;
            const chatContainer = document.querySelector('.govt-chat-container-inner');
            const pageContent = document.querySelector('.page-content-wrapper');
            
            if (chatContainer && pageContent) {
                if (isMobile) {
                    // Mobile: use more of viewport height
                    chatContainer.style.height = 'calc(100vh - 80px)';
                    chatContainer.style.minHeight = '350px';
                    pageContent.style.minHeight = 'calc(100vh - 60px)';
                } else {
                    // Desktop: use full available height
                    chatContainer.style.height = 'calc(100vh - 120px)';
                    chatContainer.style.minHeight = '500px';
                    pageContent.style.minHeight = 'calc(100vh - 80px)';
                }
            }
        }
          // Handle window resize
        window.addEventListener('resize', () => {
            adjustChatForMobile();
            // Recalculate height on orientation change
            setTimeout(adjustChatForMobile, 100);
        });
        window.addEventListener('load', adjustChatForMobile);
        
        // Handle orientation change for mobile devices
        window.addEventListener('orientationchange', () => {
            setTimeout(adjustChatForMobile, 200);
        });
        
        // Add government-style toast notifications
        function showGovtNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-information-outline me-2"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Initialize chat system
        console.log('🚀 Government Digital Communication System Ready');
    </script>
@endsection




