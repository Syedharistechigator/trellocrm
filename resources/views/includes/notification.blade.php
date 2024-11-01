<li class="dropdown notificationDropdown" id="notification-dropdown">
    <a href="javascript:void(0);" class="dropdown-toggle notification-icon" title="Notifications" role="button">
        @if(count($unreadNotifications) > 0)
            <span class="notificationCount" id="notification-dropdow-count">{{$unreadNotifications->count()}}</span>
        @endif <i class="zmdi zmdi-notifications"></i>
        <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
    </a>
    <div class="notifications-dropdown slideUp2">
        <div class="notifications-header">
            <h3>Notifications</h3>
            <button class="close-btn">&times;</button>
        </div>
        <div class="notifications-tabs">
            <button class="tab active" data-tab="board-notifications">
                <span>Board</span>
                @if($unreadBoardNotifications->count() > 0)
                    <span class="notificationCount" id="board-notification-count">
                        <span>{{ $unreadBoardNotifications->count() }}</span>
                    </span>
                @endif
            </button>
{{--                        <button class="tab" data-tab="overall">--}}
{{--                            <span>Overall</span>--}}
{{--                            @if(count($unreadNotifications) > 0)--}}
{{--                                <span class="notificationCount"  id="all-notification-count">{{$unreadNotifications->count()}}</span>--}}
{{--                            @endif--}}
{{--                        </button>--}}
            <button class="mark-read">Mark all as read</button>
        </div>
        <div class="notifications-content" id="notifications-content">
            <div class="tab-content notification-tab-content active" id="board-notifications">
                <div class="section">
                    <h4>Board Notifications</h4>
                    @foreach($unreadBoardNotifications as $unreadBoardNotification)
                        @if(isset($unreadBoardNotification->user) && $unreadBoardNotification->data['activity_type'] === 0 )
                            <div class="notification-item" id="{{$unreadBoardNotification->id}}">
                                @if(isset($unreadBoardNotification->user['image']) && file_exists(public_path('assets/images/profile_images/'). $unreadBoardNotification->user['image']))
                                    <img src="{{asset('assets/images/profile_images/'.$unreadBoardNotification->user['image'])}}" alt="Profile" class="profile-img">
                                @else
                                    <div class="icon-circle bg-blue" style="width:40px;"><i class="zmdi zmdi-account"></i></div>
                                @endif
                                <div class="notification-details">
                                    <p>
                                        <strong>{{optional($unreadBoardNotification->user)['name']}}</strong> commented on {{optional($unreadBoardNotification->board_list_card)['title']}}
                                    </p>
                                    <small>{{($unreadBoardNotification->created_at->diffForHumans())}}</small>
                                </div>
                            </div>
                        @elseif(isset($unreadBoardNotification->user) && $unreadBoardNotification->data['activity_type'] === 1 )
                            <div class="notification-item" id="{{$unreadBoardNotification->id}}">
                                @if(isset($unreadBoardNotification->user['image']) && file_exists(public_path('assets/images/profile_images/'). $unreadBoardNotification->user['image']))
                                    <img src="{{asset('assets/images/profile_images/'.$unreadBoardNotification->user['image'])}}" alt="Profile" class="profile-img">
                                @else
                                    <div class="icon-circle bg-blue" style="width:40px;"><i class="zmdi zmdi-account"></i></div>
                                @endif
                                <div class="notification-details">
                                    <p>
                                        <strong>{{optional($unreadBoardNotification->user)['name']}}</strong> {{optional($unreadBoardNotification->data)['message']?? "message not found"}}
                                    </p>
                                    <small>{{($unreadBoardNotification->created_at->diffForHumans())}}</small>
                                </div>
                            </div>
                        @elseif(isset($unreadBoardNotification->user) && $unreadBoardNotification->data['activity_type'] === 2 )
                            <div class="notification-item" id="{{$unreadBoardNotification->id}}">
                                @if(isset($unreadBoardNotification->user['image']) && file_exists(public_path('assets/images/profile_images/'). $unreadBoardNotification->user['image']))
                                    <img src="{{asset('assets/images/profile_images/'.$unreadBoardNotification->user['image'])}}" alt="Profile" class="profile-img">
                                @else
                                    <div class="icon-circle bg-blue" style="width:40px;"><i class="zmdi zmdi-account"></i></div>
                                @endif
                                <div class="notification-details">
                                    <p>
                                        <strong>{{optional($unreadBoardNotification->user)['name']}}</strong> {{optional($unreadBoardNotification->data)['message'] ?? "message not found"}}
                                    </p>
                                    <small>{{($unreadBoardNotification->created_at->diffForHumans())}}</small>
                                </div>
                            </div>
                        @else
                            <div class="notification-item" id="{{$unreadBoardNotification->id}}">
                            </div>
                        @endif
                    @endforeach
                    <!-- Repeat notification-item div for more notifications -->
                </div>
            </div>
{{--                        <div class="notification-tab-content" id="overall">--}}
{{--                            <div class="section">--}}
{{--                                <h4>All Notifications</h4>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-blue" style="width:40px;"><i class="zmdi zmdi-account"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-amber"><i class="zmdi zmdi-shopping-cart"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-red"><i class="zmdi zmdi-delete"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-green"><i class="zmdi zmdi-edit"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-grey"><i class="zmdi zmdi-comment-text"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-purple"><i class="zmdi zmdi-refresh"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <div class="icon-circle bg-light-blue"><i class="zmdi zmdi-settings"></i></div>--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <img src="http://localhost:8000/assets/images/profile_av.jpg" alt="Profile" class="profile-img">--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="notification-item">--}}
{{--                                    <img src="http://localhost:8000/assets/images/profile_av.jpg" alt="Profile" class="profile-img">--}}
{{--                                    <div class="notification-details">--}}
{{--                                        <p><strong>Mia Anders</strong> mentioned you in a comment in a thread:</p>--}}
{{--                                        <small>1h ago</small>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <!-- Repeat notification-item div for more notifications -->--}}
{{--                            </div>--}}
{{--                                                    <div class="section">--}}
{{--                                                        <h4>Yesterday</h4>--}}
{{--                                                        <div class="notification-item">--}}
{{--                                                            <img src="http://localhost:8000/assets/images/profile_av.jpg" alt="Profile" class="profile-img">--}}
{{--                                                            <div class="notification-details">--}}
{{--                                                                <p><strong>Mike Whits</strong> commented in a thread:</p>--}}
{{--                                                                <small>07/27/21 - 18:21</small>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                        <!-- Repeat notification-item div for more notifications -->--}}
{{--                                                    </div>--}}
{{--                        </div>--}}
        </div>
    </div>
</li>
<style>
    .notificationCount {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 16px;
        height: 16px;
        font-size: 12px;
        background-color: #fb0a15;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        line-height: 100%;
    }

    .notification-icon .notificationCount {
        left: 8px;
        top: 8px;
    }

    .notifications-tabs .tab.active .notificationCount {
        background-color: #007bff;
    }

    .notifications-tabs .tab .notificationCount {
        background-color: #707981;
    }

    .notification-item .icon-circle {
        color: #fff;
        text-align: center;
        margin-right: 10px;
        width: 46px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-item .icon-circle i {
        line-height: 26px;
        font-size: 24px;
        text-align: center;
    }

    .notification-wrapper {
        position: relative;
    }

    .notification-icon {
        position: relative;
        background: none;
        border: none;
        cursor: pointer;
    }

    .notifications-dropdown {
        position: absolute;
        right: 0;
        top: 100%;
        width: 360px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: none;
        z-index: 1000;
    }

    .notifications-dropdown.active {
        display: block;
    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px 5px;
        border-bottom: 1px solid #e6e6e6;
    }

    .notifications-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .notifications-header .close-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    .notifications-tabs {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-bottom: 1px solid #e6e6e6;
    }

    .notifications-tabs .tab {
        background: none;
        border: none;
        padding: 0px 10px;
        cursor: pointer;
        font-size: 14px;
        color: #6c757d;
        margin-right: 8px;
        position: relative;
    }

    .notifications-tabs .tab.active {
        color: #007bff;
        border-bottom: 2px solid #007bff;
    }

    .notifications-tabs .mark-read {
        margin-left: auto;
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        font-size: 14px;
    }

    .notifications-content {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-tab-content {
        display: none;
        padding: 16px;
    }

    .notification-tab-content.active {
        display: block;
    }

    .section {
        margin-bottom: 16px;
    }

    .section h4 {
        margin: 0 0 10px;
        font-size: 16px;
        color: #343a40;
    }

    .notification-item {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e6e6e6;
    }

    .notification-item .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .notification-details {
        flex-grow: 1;
    }

    .notification-details p {
        margin: 0;
        font-size: 14px;
        color: #343a40;
        line-height: 140%;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .notification-details span {
        display: inline-block;
        background: #e6e6e6;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 12px;
        margin-right: 8px;
    }

    .notification-details small {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
        line-height: 110%;
    }

    .notifications-footer {
        padding: 8px 16px;
        text-align: center;
        border-top: 1px solid #e6e6e6;
    }

    .notifications-footer a {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    .notifications-footer a:hover {
        text-decoration: underline;
    }
</style>
