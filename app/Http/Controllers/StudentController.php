namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function documentArchive()
    {
        // Return the view for the document archive
        return view('student.documentArchive');
    }
}
