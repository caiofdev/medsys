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
description?: string;
date: string;
}

interface DashboardScheduleProps {
events: ScheduleEvent[];
}

export default function DashboardSchedule({ events = [] }: DashboardScheduleProps) {
const todayStr = new Date().toISOString().slice(0, 10);

const futureEvents = events.filter(event => {
   const eventDay = event.date.slice(0, 10);
   return eventDay >= todayStr;
});

const eventsByDay: Record<string, ScheduleEvent[]> = {};

futureEvents.forEach(event => {
   const dateKey = event.date.slice(0, 10);
   if (!eventsByDay[dateKey]) eventsByDay[dateKey] = [];
   eventsByDay[dateKey].push(event);
});

const sortedDates = Object.keys(eventsByDay).sort();

return (
   <div className="bg-digital-blue-50 rounded-radius border border-border p-4">
      <div className="flex justify-between">
      <div className="font-bold text-lg text-darktext mb-4">
         Agenda
      </div>

      <div
         className="bg-digital-blue-100 rounded-full h-fit w-fit p-1 cursor-pointer hover:bg-digital-blue-200"
         onClick={() => router.visit('/calendar')}
      >
         <Plus />
      </div>
      </div>

      <Swiper
      modules={[Scrollbar, Mousewheel]}
      slidesPerView={3}
      direction="vertical"
      scrollbar={{ draggable: true }}
      mousewheel
      spaceBetween={0}
      className="h-[250px]"
      >
      {sortedDates.flatMap(dateStr => [
         <SwiperSlide key={`${dateStr}-divider`} className='h-fit!'>
            <div className=" font-semibold text-digital-blue-600 text-sm mb-4 mt-2 border border-digital-blue-300 bg-white/60 p-1 px-3 rounded-2xl w-fit capitalize">
            {new Date(`${dateStr}T12:00:00`).toLocaleDateString('pt-BR', {
               weekday: 'long',
               day: '2-digit',
               month: '2-digit',
               year: 'numeric',
            })}
            </div>
         </SwiperSlide>,

         ...eventsByDay[dateStr].map((event, index) => (
            <SwiperSlide key={event.id}>
            <DashboardScheduleEvent
               index={index}
               date={event.time}
               title={event.title}
               description={event.description || ''}
            />
            </SwiperSlide>
         )),
      ])}
      </Swiper>
   </div>
);
}
