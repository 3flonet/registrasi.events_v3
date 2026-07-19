<div class="min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto w-full px-4">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 text-center p-12">
            
            <div class="mb-6">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                    <svg class="h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">Terima Kasih!</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Permohonan inquiry Anda telah kami terima. Tim kami akan segera meninjau data Anda dan menghubungi Anda kembali.
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left inline-block w-full max-w-lg">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 text-sm">Submission ID:</span>
                    <span class="font-mono font-medium">#{{ $submission->id }}</span>
                </div>
                 <div class="flex justify-between mb-2">
                    <span class="text-gray-500 text-sm">Inquiry Type:</span>
                    <span class="font-medium">{{ $submission->form->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Status:</span>
                    <span class="text-yellow-600 font-bold uppercase text-xs tracking-wider">Pending Review</span>
                </div>
            </div>

            @if($proposalUrl)
                <div class="space-y-4">
                    <p class="text-gray-600">Silakan unduh proposal penawaran kami di bawah ini untuk informasi lebih detail.</p>
                    <a href="{{ $proposalUrl }}" target="_blank" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Proposal PDF
                    </a>
                </div>
            @endif
            
            <div class="mt-12 pt-8 border-t border-gray-100">
                <a href="{{ route('public.inquiry.landing') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; Kembali ke Halaman Utama Inquiry
                </a>
            </div>

        </div>
    </div>
</div>
