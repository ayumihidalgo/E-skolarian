@extends('base')

@section('content')
    <!-- HOW TO USE the Header-->
    <h1> E-SKOLARIAN </h1>
    <h2> E-SKOLARIAN </h2>
    <h3> E-SKOLARIAN </h3>
    <h4> E-SKOLARIAN </h4>
    <h5> E-SKOLARIAN </h5>
    <h6> E-SKOLARIAN </h6>

    <h1>Tables and Buttons Example</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>25</td>
                <td>john.doe@example.com</td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td>30</td>
                <td>jane.smith@example.com</td>
            </tr>
        </tbody>
    </table>
    <br>
    <button onclick="alert('Hello, World!')">Click Me</button>

    <h1>Media Embedding Example</h1> <img src="https://via.placeholder.com/300" alt="Placeholder Image"> <br> <audio controls>
        <source src="example.mp3" type="audio/mpeg"> Your browser does not support the audio tag.
    </audio> <br> <video controls width="400">
        <source src="example.mp4" type="video/mp4"> Your browser does not support the video tag.
    </video>

    <h1>Registration Form</h1>
    <form action="/submit" method="POST"> <label for="name">Name:</label> <input type="text" id="name"
            name="name" required> <br> <label for="email">Email:</label> <input type="email" id="email"
            name="email" required> <br> <label for="password">Password:</label> <input type="password" id="password"
            name="password" required> <br> <button type="submit">Register</button> </form>

    <h1>Welcome to HTML Basics</h1>
    <p>This is a paragraph providing a brief description.</p> <a href="https://laravel.com/">Learn more about Laravel</a>
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
    </ul>
@endsection
