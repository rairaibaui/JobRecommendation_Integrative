<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Job Recommendations - Job Recommendation System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">🤖 AI Job Recommendations</h1>
                        <p class="text-gray-600 mt-2">Personalized job matches powered by artificial intelligence</p>
                    </div>
                    <button onclick="refreshRecommendations()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            @include('partials.trust-banner')

            <!-- Career Insights -->
            @if($careerInsights)
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-md p-6 mb-6 text-white">
                <h2 class="text-2xl font-bold mb-4">💡 Career Insights</h2>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <p class="whitespace-pre-line">{{ $careerInsights }}</p>
                </div>
            </div>
            @endif

            <!-- Recommendations Count -->
            <div class="mb-6">
                <p class="text-gray-700 text-lg">
                    Found <span class="font-bold text-blue-600">{{ count($recommendations) }}</span> AI-recommended jobs for you
                </p>
            </div>

            <!-- Job Recommendations -->
            @if(count($recommendations) > 0)
                <div class="space-y-4">
                    @foreach($recommendations as $index => $job)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <!-- Match Badge -->
                                    <div class="flex items-center mb-3">
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            #{{ $index + 1 }} Match
                                        </span>
                                        <span class="ml-2 text-2xl font-bold 
                                            @if($job['match_score'] >= 80) text-green-600
                                            @elseif($job['match_score'] >= 60) text-blue-600
                                            @else text-yellow-600
                                            @endif">
                                            {{ $job['match_score'] }}%
                                        </span>
                                    </div>

                                    <!-- Job Title & Company -->
                                    <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $job['title'] }}</h3>
                                    <p class="text-lg text-gray-600 mb-4">{{ $job['company'] }}</p>

                                    <!-- Job Details -->
                                    <div class="flex flex-wrap gap-4 mb-4">
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $job['location'] }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $job['type'] }}
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $job['salary'] }}
                                        </div>
                                    </div>

                                    <!-- AI Explanation -->
                                    @if(!empty($job['ai_explanation']))
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                                        <p class="text-sm font-semibold text-blue-800 mb-1">Why this job?</p>
                                        <p class="text-gray-700">{{ $job['ai_explanation'] }}</p>
                                    </div>
                                    @endif

                                    <!-- Matching Skills -->
                                    @if(!empty($job['matching_skills']) && count($job['matching_skills']) > 0)
                                    <div class="mb-4">
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Your matching skills:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($job['matching_skills'] as $skill)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                                ✓ {{ ucfirst($skill) }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Career Growth -->
                                    @if(!empty($job['career_growth']))
                                    <div class="bg-purple-50 p-3 rounded-lg mb-4">
                                        <p class="text-sm font-semibold text-purple-800 mb-1">Career Growth Potential:</p>
                                        <p class="text-gray-700 text-sm">{{ $job['career_growth'] }}</p>
                                    </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3 mt-4">
                                        <a href="{{ route('job.apply') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                            Apply Now
                                        </a>
                                        <button onclick="bookmarkJob('{{ $job['title'] }}')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold transition-colors">
                                            Bookmark
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800">No Recommendations Yet</h3>
                            <p class="text-yellow-700 mt-1">Complete your profile with skills and experience to get AI-powered job recommendations!</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        function refreshRecommendations() {
            if (confirm('Refresh AI recommendations? This will generate new suggestions.')) {
                fetch('{{ route("ai.refresh") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Failed to refresh: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while refreshing recommendations');
                });
            }
        }

        function bookmarkJob(jobTitle) {
            // Implement bookmark functionality
            alert('Bookmark feature for: ' + jobTitle);
        }
    </script>
</body>
</html>
