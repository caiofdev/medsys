import EmployeeCard from "./employee-card";
import { Swiper, SwiperSlide } from 'swiper/react';
import { Scrollbar, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/scrollbar';

interface DashboardEmployeesProps {
   users: {
      id: number;
      name: string;
      role: string;
      email: string;
   }[]
}

export default function DashboardEmployees({users}: DashboardEmployeesProps) {
   return (
      <div className="rounded-radius bg-digital-blue-50 border border-border flex flex-col h-full max-h-253 xl:max-h-228">
         <div className="p-4 border-b border-border">
            <h2 className="text-xl font-bold text-darktext">Equipe</h2>
            <p className="text-sm text-gray-500 mt-1">{users.length} {users.length === 1 ? 'funcionário' : 'funcionários'}</p>
         </div>
         <div className="flex-1 p-4 overflow-hidden">
            {users.length > 0 ? (
               <Swiper
                  modules={[Scrollbar, Mousewheel]}
                  slidesPerView={'auto'}
                  direction="vertical"
                  scrollbar={{ draggable: true }}
                  mousewheel={true}
                  spaceBetween={12}
                  className="h-full"
               >
                  {users.map((user, index) => (
                     <SwiperSlide key={index} style={{ height: 'auto' }}>
                        <EmployeeCard user={user} />
                     </SwiperSlide>
                  ))}
               </Swiper>
            ) : (
               <div className="flex items-center justify-center h-full">
                  <p className="text-gray-500 text-sm">Nenhum funcionário cadastrado</p>
               </div>
            )}
         </div>
      </div>
   );
}