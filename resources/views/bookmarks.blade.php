@extends('layouts.recommendation')

@section('content')
    <div class="main">
        <div class="top-navbar">
            <i class="fas fa-bars hamburger"></i>
            Job Portal - Mandaluyong
        </div>

        <!-- Bookmarked Jobs -->
        <div class="card-large">
            <div class="recommendation-header">
                <h3 style="color: #FFFFFF; font-size: 28px;">Bookmarked Jobs</h3>
                <p style="color: #FFFFFF; font-size: 20px;">Jobs you've saved for later.</p>
            </div>

            <div class="jobs-grid">
                @if(empty($bookmarks) || count($bookmarks) === 0)
                    <div class="no-bookmarks">
                        <i class="fas fa-bookmark no-bookmarks-icon"></i>
                        <h4 class="no-bookmarks-title">No Bookmarks</h4>
                        <p class="no-bookmarks-text">You haven't saved any jobs yet.</p>
                        <div class="no-bookmarks-rectangle">
                            <a href="{{ route('recommendation') }}" class="browse-link">Browse Job Recommendations</a>
                        </div>
                    </div>
                @else
                    @foreach($bookmarks as $job)
                        <div class="job-card">
                            <div class="job-title">{{ $job['title'] }}</div>
                            <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job['location'] ?? 'N/A' }}</div>
                            <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job['type'] ?? 'Full-time' }}</div>
                            <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job['salary'] ?? 'Negotiable' }}
                            </div>
                            <div class="job-description">{{ $job['description'] ?? '' }}</div>
                            <div class="skills-header"><strong>Skills Required:</strong></div>
                            <div class="job-skills">
                                @if(!empty($job['skills']))
                                    @foreach($job['skills'] as $skill)
                                        <div class="skill">{{ $skill }}</div>
                                    @endforeach
                                @else
                                    <div class="skill">No specific skills listed</div>
                                @endif
                            </div>
                            <div class="job-actions">
                                <button class="view-details">View Details</button>
                                <button class="save-job">Remove Bookmark</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- 🌟 Hover + Focus Effect for Search Bar -->
    <style>
        .inner-search-box:hover {
            border-color: #648EB5;
            /* soft blue hover */
        }

        .inner-search-box:focus-within {
            border-color: #406482;
            /* darker blue focus */
        }

        .no-bookmarks {
            width: 100%;
            max-width: 1200px;
            height: 500px;
            margin: 0 auto;
            border-radius: 8px;
            background: #FFF;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .no-bookmarks-icon {
            font-size: 115px;
            color: #ccc;
            margin-bottom: 20px;
            -webkit-text-stroke: 2px #999;
        }

        .no-bookmarks-title {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }

        .no-bookmarks-text {
            font-size: 20px;
            color: #666;
        }

        .no-bookmarks-rectangle {
            border-radius: 8px;
            border: 1px solid #648EB5;
            background: #648EB5;
            box-shadow: 0 6px 4px 0 rgba(0, 0, 0, 0.25);
            width: 250px;
            height: 50px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .browse-link {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .job-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            background-color: #f9f9f9;
        }

        .job-title {
            font-size: 22px;
            font-weight: bold;
        }

        .job-location,
        .job-type,
        .job-salary {
            font-size: 16px;
            margin: 3px 0;
        }

        .job-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .skill {
            background: #e0e0e0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        .job-actions {
            margin-top: 10px;
        }

        .job-actions button {
            margin-right: 10px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .view-details {
            background-color: #648EB5;
            color: #fff;
        }

        .save-job {
            background-color: #ccc;
        }
    </style>
@endsection