import PatientCard from "./patient-card";
import { Swiper, SwiperSlide } from 'swiper/react';
import { Scrollbar, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/scrollbar';

interface DashboardPatientsListProps {
   patients: {
      name: string;
      birth_date: string;
      email: string;
   }[]
}

export default function DashboardPatientsList({patients}: DashboardPatientsListProps) {
   return (
      <div className="rounded-radius bg-digital-blue-50 border border-border flex flex-col h-full">
         <div className="p-4 border-b border-border">
            <h2 className="text-xl font-bold text-darktext">Seus pacientes</h2>
            <p className="text-sm text-gray-500 mt-1">{patients.length} {patients.length === 1 ? 'paciente' : 'pacientes'}</p>
         </div>
         <div className="flex-1 p-4 overflow-hidden">
            {patients.length > 0 ? (
               <Swiper
                  modules={[Scrollbar, Mousewheel]}
                  slidesPerView={'auto'}
                  direction="vertical"
                  scrollbar={{ draggable: true }}
                  mousewheel={true}
                  spaceBetween={12}
                  className="h-full"
               >
                  {patients.map((patient, index) => (
                     <SwiperSlide key={index} style={{ height: 'auto' }}>
                        <PatientCard patient={patient} />
                     </SwiperSlide>
                  ))}
               </Swiper>
            ) : (
               <div className="flex items-center justify-center h-full">
                  <p className="text-gray-500 text-sm">Nenhum paciente cadastrado</p>
               </div>
            )}
         </div>
      </div>
   );
}