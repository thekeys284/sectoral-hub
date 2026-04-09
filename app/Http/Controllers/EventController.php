<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Kegiatan;

class EventController extends Controller
{
    public function index(){
        $events = Event::latest()->get();
        return view('data.event.index', compact('events'));  
    }

    public function create() {
        $event = new Event();
        return view('data.event.form', compact('event'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'lokasi_event' => 'required|string',
            'category' => 'required|string',
            'is_active' => 'required|boolean',
            'image_banner'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'virtual_bg'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data=$request->all();
        $data['created_by'] = Auth::id();

        if($request->hasFile('image_banner')){
            $file=$request->file('image_banner');
            $filename=time().'_banner_'.$file->getClientOriginalName();
            $file->move(public_path('events'),$filename);
            $data['image_banner']='events/'.$filename;
        }

        if ($request->hasFile('virtual_bg')) {
            $fileBg = $request->file('virtual_bg');
            $bgName = time() . '_vbg_' . $fileBg->getClientOriginalName();
            $fileBg->move(public_path('events/virtual_bg'), $bgName);
            $data['virtual_bg'] = 'events/virtual_bg/' . $bgName;
        }

        Event::create($data);

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event) {
        return view('data.event.form', compact('event'));
    }

    public function show(Event $event)
    {        
        return view('data.event.show', compact('event'));
    }

    public function update(Request $request, Event $event){
        $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'lokasi_event' => 'required|string',
            'category' => 'required|string',
            'is_active' => 'required|boolean',
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'virtual_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_banner')) {
            if ($event->image_banner && file_exists(public_path($event->image_banner))) {
                unlink(public_path($event->image_banner));
            }
            $file = $request->file('image_banner');
            $fileName = time() . '_banner_' . $file->getClientOriginalName();
            $file->move(public_path('events'), $fileName);
            $data['image_banner'] = 'events/' . $fileName;
        }

        if ($request->hasFile('virtual_bg')) {
            if ($event->virtual_bg && file_exists(public_path($event->virtual_bg))) {
                unlink(public_path($event->virtual_bg));
            }
            $fileBg = $request->file('virtual_bg');
            $bgName = time() . '_vbg_' . $fileBg->getClientOriginalName();
            $fileBg->move(public_path('events/virtual_bg'), $bgName);
            $data['virtual_bg'] = 'events/virtual_bg/' . $bgName;
        }

        $event->update($data);

        return redirect()->route('event.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event){
        if ($event->image_banner && file_exists(public_path($event->image_banner))) {
            unlink(public_path($event->image_banner));
        }
        // Hapus file virtual background
        if ($event->virtual_bg && file_exists(public_path($event->virtual_bg))) {
            unlink(public_path($event->virtual_bg));
        }

        $event->delete();
        return redirect()->route('event.index')->with('success', 'Event Berhasil Dihapus');   
    }

    public function whatsnext()
    {
        $nextEvents = Event::where('start_at', '>=', now())
                            ->orderBy('start_at', 'asc')
                            ->take(5)
                            ->get();

        $stats = [
            'total_kegiatan' => Kegiatan::count(),
            'event_active'   => Event::where('start_at', '>=', now())->count(),
        ];

        return view('pages.whatsnext', compact('nextEvents', 'stats'));
    }
}
