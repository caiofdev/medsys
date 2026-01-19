import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import AppLayout from '@/layouts/app-layout'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/react'

const breadcrumbs: BreadcrumbItem[] = [
   {
      title: 'Calendário',
      href: '/calendar',
   },
]

const events = [
   { title: 'Meeting', start: new Date() }
]

export default function Calendar() {
   return (
      <AppLayout breadcrumbs={breadcrumbs}>
         <Head title="Calendário" />
         <div className="min-h-screen bg-background p-10">
            <div className="max-w-7xl mx-auto">
               <div className="mb-6 ">
                  <h1 className="text-3xl font-bold text-digital-blue-900">Calendário</h1>
                  <p className="text-gray-600 mt-2">Visualize e gerencie seus compromissos</p>
               </div>
               
               <div className=" bg-white-300 rounded-radius border border-border p-10 shadow-sm text-darktext">
                  <FullCalendar
                     plugins={[dayGridPlugin]}
                     initialView='dayGridMonth'
                     weekends={true}
                     events={events}
                     eventContent={renderEventContent}
                     headerToolbar={{
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek,dayGridDay'
                     }}
                     height="auto"
                     locale="pt-br"
                     buttonText={{
                        today: 'Hoje',
                        month: 'Mês',
                        week: 'Semana',
                        day: 'Dia'
                     }}
                     dayHeaderFormat={{ weekday: 'short' }}
                     eventClassNames="bg-digital-blue-300 cursor-pointer "
                  />
               </div>
            </div>
         </div>
      </AppLayout>
   )
}

function renderEventContent() {
   return (
      <div className="p-1">
         <b className="text-xs">Exemplo</b>
         <div className="text-xs">Descricao</div>
      </div>
   )
}