@extends('layouts.recommendation')

@section('content')
<div class="main">
    <div class="top-navbar">
        <i class="fas fa-bars hamburger"></i>
        Job Portal - Mandaluyong
    </div>

    <!-- Top Job Recommendations -->
    <div class="card-large">
        <div class="recommendation-header">
            <h3 style="color: #FFFFFF;">Job Recommendations</h3>
            <p style="color: #FFFFFF;">Jobs matched to your skills and preferences.</p>
        </div>

        <!-- 🔍 Search Bar Container -->
        <div style="
            width: 898px; 
            height: 74px; 
            flex-shrink: 0; 
            border-radius: 8px; 
            background: #FFF; 
            /* removed outer border */
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 0 20px;
        ">
            <!-- Inner Search Box (now has the stroke) -->
            <div class="inner-search-box" style="
                width: 850px; 
                height: 50px; 
                background: #F5F5F5; 
                border-radius: 8px; 
                display: flex; 
                align-items: center; 
                padding: 0 15px; 
                border: 2px solid #000; /* moved stroke here */
                transition: all 0.3s ease;
            ">
                <i class="fas fa-search" 
                   style="font-size: 20px; color: #888; margin-right: 10px;">
                </i>

                <input type="text" placeholder="Search for jobs..." 
                    style="
                        flex: 1; 
                        border: none; 
                        outline: none; 
                        background: transparent; 
                        font-size: 18px; 
                        font-family: 'Roboto', sans-serif;
                    ">
            </div>
        </div>

        <p style="font-family: 'Roboto', sans-serif; font-size: 20px; color: #FFF; margin-bottom: 10px;">Showing 2 jobs</p>
        
        <div class="jobs-grid">
            @foreach($jobs as $job)
            <div class="job-card">
                <div class="job-title">{{ $job['title'] }}</div>
                <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job['location'] }}</div>
                <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job['type'] }}</div>
                <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job['salary'] }}</div>
                <div class="job-description">{{ $job['description'] }}</div>
                <div class="skills-header"><strong>Skills Required:</strong></div>
                <div class="job-skills">
                    @foreach($job['skills'] as $skill)
                    <div class="skill">{{ $skill }}</div>
                    @endforeach
                </div>
                <div class="job-actions">
                    <button class="view-details">View Details</button>
                    <button class="save-job">Save Job</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 🌟 Hover + Focus Effect for Search Bar -->
<style>
.inner-search-box:hover {
    border-color: #648EB5; /* soft blue hover */

}
.inner-search-box:focus-within {
    border-color: #406482; /* darker blue focus */

}
</style>
@endsection
