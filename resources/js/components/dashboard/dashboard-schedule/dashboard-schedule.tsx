import { Plus } from 'lucide-react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Scrollbar, Mousewheel } from 'swiper/modules';
import { router } from '@inertiajs/react';
import 'swiper/css';
import 'swiper/css/scrollbar';
import DashboardScheduleEvent from './dashboard-schedule-event';

interface ScheduleEvent {
   id: number;
   time: string;
   title: string;
   description: string;
}

interface DashboardScheduleProps {
   events: ScheduleEvent[];
}

export default function DashboardSchedule({ events }: DashboardScheduleProps) {
   return(
      <div className="bg-digital-blue-50 rounded-radius border border-border p-4">
         <div className=" flex justify-between">
            <div className='font-bold text-lg text-darktext mb-4'>
               Agenda
            </div>
            <div className='bg-digital-blue-100 rounded-full h-fit w-fit p-1 cursor-pointer hover:bg-digital-blue-200' onClick={() => router.visit('/calendar')}>
               <Plus />
            </div>
         </div>
         <div>
            <Swiper
               modules={[Scrollbar, Mousewheel]}
               slidesPerView={3}
                  direction="vertical"
                  scrollbar={{ draggable: true}}
                  mousewheel={true}
                  spaceBetween={12}
               className="h-[250px]"
            >
               {events.map((event, index) => (
                  <SwiperSlide key={event.id}>
                        <DashboardScheduleEvent 
                           index={index} 
                           date={event.time} 
                           title={event.title} 
                           description={event.description} 
                        />
                  </SwiperSlide>
               ))}
            </Swiper>
         </div>
      </div>
   );
}