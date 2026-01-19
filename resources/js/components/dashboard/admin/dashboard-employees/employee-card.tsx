import { Settings2 } from 'lucide-react';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

export interface EmployeeCardProps {
   user: {
      name: string;
      role: string;
      email: string;
   };
}
const colorsRole = {
   admin: { bg: 'bg-digital-blue-800', text: 'text-digital-blue-800' },
   doctor: { bg: 'bg-digital-blue-600', text: 'text-digital-blue-600' },
   receptionist: { bg: 'bg-digital-blue-400', text: 'text-digital-blue-400' },
};

export default function EmployeeCard({ user }: EmployeeCardProps) {
   const roleColors = user.role === 'admin' 
      ? colorsRole.admin 
      : user.role === 'doctor' 
      ? colorsRole.doctor 
      : colorsRole.receptionist;

   return (
      <div className="flex bg-digital-blue-100 rounded-radius px-3 py-3 items-center border border-border justify-between hover:shadow-md transition-all duration-200 cursor-pointer">
         <div className='flex items-center gap-3'>
            <div className='w-fit rounded-full'>
                  <Avatar className='h-10 w-10'>
                     <AvatarFallback className={`${roleColors.bg} text-white font-semibold`}>
                        {user.name.charAt(0)}
                     </AvatarFallback>
                  </Avatar>
            </div>
            <div className='flex flex-col justify-center'>
                  <p className="text-darktext font-semibold">{user.name}</p>
                  <p className="text-gray-600 text-xs">{user.role === 'admin' ? 'Administrador' : user.role === 'doctor' ? 'Médico' : 'Recepcionista'}</p>
            </div>
         </div>
         <div className='flex-shrink-0'>
            <Settings2 size={18} className={`cursor-pointer hover:scale-104 ${roleColors.text} transition-transform`}/>
         </div>
      </div>
   );
}