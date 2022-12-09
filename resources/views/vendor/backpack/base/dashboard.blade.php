@extends(backpack_view('blank'))

@php
    $widgets['before_content'][] = [
        'type'          => 'progress',
    'class'         => 'card bg-primary mb-2',
    'value'         => \App\Models\User::count(),
    'description'   => 'Registered users.',
    'progressClass' => 'progress-bar bg-light',
    'progress'=>0,
    'footer_link'=>route('user.index'),
    'wrapper' => [
    'style' => 'float:left',
]
    ];
     $widgets['before_content'][] = [
       'type'          => 'progress',
    'class'         => 'card bg-success mb-2',
    'value'         => \App\Models\Post::count(),
    'description'   => 'Total posts.',
    'progress'=>0,
    'progressClass' => 'progress-bar bg-light',
    'footer_link'=>route('post.index'),
     'wrapper' => [
    'style' => 'float:left',
]
    ];
      $widgets['before_content'][] = [
       'type'          => 'progress',
    'class'         => 'card bg-dark mb-2',
    'value'         => \App\Models\Tag::count(),
    'description'   => 'Total tags.',
    'progressClass' => 'progress-bar progress-white',
    'progress'=>0,
   'footer_link'=>route('tag.index'),
 'wrapper' => [
    'style' => 'float:left',
]
    ];


@endphp

@section('content')
@endsection
